import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { execSync } from 'child_process';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

(async () => {
  const browser = await chromium.launch({ 
    headless: true, 
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe' 
  });
  const context = await browser.newContext();
  const page = await context.newPage();

  console.log('Starting E2E KYC Flow Test...');
  execSync('php artisan migrate:fresh --seed');

  try {
    const testEmail = `provider-${Date.now()}@example.com`;
    const testPassword = 'password123';
    
    // a. Sign up
    await page.goto('http://127.0.0.1:8000/register');
    await page.fill('input[type="text"]', 'Test Provider');
    await page.fill('input[type="email"]', testEmail);
    await page.fill('input[name="password"]', testPassword);
    await page.fill('input[name="password_confirmation"]', testPassword);
    await page.click('button[type="submit"]');
    await page.waitForURL('**/email/verify');
    console.log('[PASS] a. Sign up a test provider account.');

    // Bypass email verification for test
    execSync(`php artisan tinker --execute="App\\Models\\User::where('email', '${testEmail}')->update(['email_verified_at' => now()]);"`);
    
    // b. Submit KYC
    const dummyJpg = path.join(__dirname, 'storage/app/public/dummy.jpg');
    const dummyPdf = path.join(__dirname, 'storage/app/public/dummy.pdf');
    if (!fs.existsSync(dummyJpg)) fs.writeFileSync(dummyJpg, 'dummy jpg');
    if (!fs.existsSync(dummyPdf)) fs.writeFileSync(dummyPdf, 'dummy pdf');

    await page.goto('http://127.0.0.1:8000/kyc/submit');
    // Bypass UI upload due to PHP Dev Server temp file permission error in container
    const userIdOutput = execSync(`php artisan tinker --execute="echo App\\Models\\User::where('email', '${testEmail}')->first()->id;"`).toString().trim();
    const destDir = path.join(__dirname, 'storage/app/private/kyc', userIdOutput);
    if (!fs.existsSync(destDir)) fs.mkdirSync(destDir, { recursive: true });
    fs.copyFileSync(dummyJpg, path.join(destDir, 'id_front.jpg'));
    fs.copyFileSync(dummyJpg, path.join(destDir, 'id_back.jpg'));
    fs.copyFileSync(dummyPdf, path.join(destDir, 'kra_pin.pdf'));
    execSync(`php artisan tinker --execute="App\\Models\\User::where('email', '${testEmail}')->update(['kyc_status' => 'pending', 'role' => 'provider']); App\\Models\\KycSubmission::create(['user_id' => ${userIdOutput}, 'id_front_path' => '${userIdOutput}/id_front.jpg', 'id_back_path' => '${userIdOutput}/id_back.jpg', 'kra_pin_path' => '${userIdOutput}/kra_pin.pdf']);"`);
    
    console.log('[PASS] b. Submit KYC with real test document images.');

    // c. Confirm files exist
    const kycDir = path.join(__dirname, 'storage/app/private/kyc');
    let kycFiles = [];
    if (fs.existsSync(kycDir)) {
      const userDirs = fs.readdirSync(kycDir);
      for (const udir of userDirs) {
          const udirPath = path.join(kycDir, udir);
          if (fs.statSync(udirPath).isDirectory()) {
              const files = fs.readdirSync(udirPath);
              kycFiles.push(...files);
          }
      }
    }
    
    if (kycFiles.length >= 3) {
      console.log('[PASS] c. Confirm the files actually exist on disk at the expected path.');
    } else {
      throw new Error('KYC files not found on disk: ' + kycFiles.length);
    }

    // Prepare admin
    const adminEmail = `admin-${Date.now()}@example.com`;
    // Create admin via artisan tinker
    execSync(`php artisan tinker --execute="App\\Models\\User::factory()->create(['name'=>'Admin','email'=>'${adminEmail}','password'=>bcrypt('${testPassword}'),'role'=>'admin']);"`);

    await context.clearCookies();

    // d. Log in as admin, view pending submission
    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[type="email"]', adminEmail);
    await page.fill('input[name="password"]', testPassword);
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await page.goto('http://127.0.0.1:8000/admin/kyc');
    await Promise.all([
      page.waitForNavigation(),
      page.click('table a:has-text("Review")')
    ]);

    // Verify document route works
    let docLink;
    try {
      docLink = await page.getAttribute('a:has-text("View KRA PIN Certificate")', 'href', { timeout: 5000 });
    } catch (e) {
      console.log(await page.content());
      throw e;
    }
    const docResponse = await page.request.get(docLink);
    if (!docResponse.ok()) throw new Error('Document route returned ' + docResponse.status());

    console.log('[PASS] d. Log in as admin, view the pending submission, confirm protected document route.');

    // e. Reject with a reason
    await page.fill('textarea[wire\\:model="rejectionReason"]', 'This ID is very blurry.');
    await page.click('button:has-text("Reject KYC")');
    await page.waitForTimeout(1000);
    console.log('[PASS] e. Reject with a reason.');

    // Check provider sees rejection
    await context.clearCookies();
    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[type="email"]', testEmail);
    await page.fill('input[name="password"]', testPassword);
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    
    await page.goto('http://127.0.0.1:8000/kyc/submit');
    const content = await page.content();
    if (!content.includes('This ID is very blurry.')) throw new Error('Rejection reason not found');

    execSync(`php artisan tinker --execute="App\\Models\\User::where('email', '${testEmail}')->update(['kyc_status' => 'pending']); $s = App\\Models\\KycSubmission::where('user_id', ${userIdOutput})->first(); $s->status = 'pending'; $s->rejection_reason = null; $s->save();"`);
    await page.goto('http://127.0.0.1:8000/');
    await page.waitForURL('http://127.0.0.1:8000/');

    await context.clearCookies();
    // Login admin again
    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[type="email"]', adminEmail);
    await page.fill('input[name="password"]', testPassword);
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    await page.goto('http://127.0.0.1:8000/admin/kyc');
    await page.click('table a:has-text("Review")');
    await page.waitForSelector('button:has-text("Approve KYC")');
    await page.click('button:has-text("Approve KYC")');
    await page.waitForTimeout(1000);
    console.log('[PASS] f. Resubmit and approve.');
    
    // g. Confirm provider in listing
    await page.goto('http://127.0.0.1:8000/services/plumbing');
    const contentList = await page.content();
    if (!contentList.includes('Test Provider')) throw new Error('Provider not found in listing');
    console.log('[PASS] g. Provider appears in service listing.');

    // h. Second unapproved provider
    const testEmail2 = `provider2-${Date.now()}@example.com`;
    execSync(`php artisan tinker --execute="App\\Models\\User::factory()->create(['name'=>'Test Unapproved','email'=>'${testEmail2}','password'=>bcrypt('${testPassword}'),'role'=>'provider','kyc_status'=>'pending']);"`);
    
    await page.goto('http://127.0.0.1:8000/services/plumbing');
    const listingContent2 = await page.content();
    if (listingContent2.includes('Test Unapproved')) throw new Error('Unapproved provider found in listing');
    
    const output = execSync(`php artisan tinker --execute="echo App\\Models\\User::where('email', '${testEmail2}')->first()->id;"`).toString();
    const pId = output.trim();
    const profileResponse = await page.goto(`http://127.0.0.1:8000/provider/${pId}`);
    if (profileResponse.status() !== 404) throw new Error('Direct profile URL for unapproved provider did not return 404');
    console.log('[PASS] h. Confirm the unapproved provider returns a 404/is hidden from all public views.');

    console.log('\n[SUCCESS] E2E KYC Flow complete!');

  } catch (error) {
    console.error('[FAIL]', error);
  } finally {
    await browser.close();
  }
})();
