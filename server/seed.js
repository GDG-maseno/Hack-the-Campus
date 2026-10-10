import { openDb } from './db.js';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const DB_PATH = path.join(__dirname, '..', 'fixmaseno.db');

const RESET = process.argv.includes('--reset');

const db = openDb(DB_PATH);

if (RESET) {
  db.exec("DELETE FROM votes; DELETE FROM issues; DELETE FROM sqlite_sequence WHERE name IN ('issues');");
  console.log('Database wiped.');
}

function iso(daysAgo, hoursAgo = 0) {
  const d = new Date();
  d.setDate(d.getDate() - daysAgo);
  d.setHours(d.getHours() - hoursAgo);
  return d.toISOString().replace(/\.\d{3}Z$/, 'Z');
}

const issues = [
  {
    title: 'Burst pipe outside Hostel B',
    summary: 'High-pressure water pipe has burst on the walkway outside Hostel B, causing flooding and blocking pedestrian access.',
    original_text: 'Bomba imepasuka nje ya Hostel B maji inatoka mob sana tangu asubuhi, njia yote imefurika wanafunzi hawawezi kupita.',
    location: 'Hostel B',
    category: 'water',
    urgency: 'critical',
    department: 'Estates & Maintenance (Water & Plumbing)',
    language: 'Sheng',
    confidence: 'high',
    status: 'open',
    upvotes: 19,
    photo: null,
    ai_source: 'seed',
    created_at: iso(0, 2),
    updated_at: iso(0, 2),
    resolved_at: null,
  },
  {
    title: 'Path light not working near Library',
    summary: 'Security light on the pedestrian path near the Library entrance is dead, creating a safety hazard at night.',
    original_text: 'Taa ya njia karibu na Library imezima, usiku ni giza sana na wanafunzi wanaogopa kupita.',
    location: 'Library',
    category: 'security',
    urgency: 'high',
    department: 'Security Services',
    language: 'Swahili',
    confidence: 'high',
    status: 'in_progress',
    upvotes: 14,
    photo: null,
    ai_source: 'seed',
    created_at: iso(1),
    updated_at: iso(0, 5),
    resolved_at: null,
  },
  {
    title: 'Lab 5 PC 14 won\'t boot',
    summary: 'Computer workstation 14 in Lab 5 fails to boot; students cannot use it for coursework.',
    original_text: 'PC number 14 kwa Lab 5 haiboot kabisa, imekufa. Tunahitaji kwa assignment.',
    location: 'Lab 5',
    category: 'ict',
    urgency: 'medium',
    department: 'ICT Services',
    language: 'Sheng',
    confidence: 'high',
    status: 'open',
    upvotes: 8,
    photo: null,
    ai_source: 'seed',
    created_at: iso(0, 3),
    updated_at: iso(0, 3),
    resolved_at: null,
  },
  {
    title: 'Blocked drain at Cafeteria',
    summary: 'Drainage channel outside the Cafeteria is fully blocked, causing wastewater overflow onto the walkway.',
    original_text: 'Mfereji nje ya Cafeteria umejaa taka na maji machafu yanafurika barabara. Inasumbua sana.',
    location: 'Cafeteria',
    category: 'sanitation',
    urgency: 'high',
    department: 'Cleaning & Sanitation Services',
    language: 'Swahili',
    confidence: 'high',
    status: 'open',
    upvotes: 22,
    photo: null,
    ai_source: 'seed',
    created_at: iso(1, 4),
    updated_at: iso(1, 4),
    resolved_at: null,
  },
  {
    title: 'Exposed socket wiring in Lecture Halls',
    summary: 'Electrical socket in Lecture Hall Block C has exposed live wiring, posing an electrocution risk.',
    original_text: 'Socket kwa Lecture Halls Block C imevunjika na waya za stima zinaonekana nje, ni hatari sana.',
    location: 'Lecture Halls',
    category: 'electrical',
    urgency: 'critical',
    department: 'Estates & Maintenance (Electrical)',
    language: 'Sheng',
    confidence: 'high',
    status: 'open',
    upvotes: 31,
    photo: null,
    ai_source: 'seed',
    created_at: iso(0, 1),
    updated_at: iso(0, 1),
    resolved_at: null,
  },
  {
    title: 'Projector no signal in School of Computing',
    summary: 'Projector in the main lecture room of the School of Computing shows no signal; lectures cannot proceed.',
    original_text: 'The projector in School of Computing main hall shows no signal. Lecturer cannot teach, whole class affected.',
    location: 'School of Computing',
    category: 'ict',
    urgency: 'medium',
    department: 'ICT Services',
    language: 'English',
    confidence: 'high',
    status: 'open',
    upvotes: 11,
    photo: null,
    ai_source: 'seed',
    created_at: iso(0, 6),
    updated_at: iso(0, 6),
    resolved_at: null,
  },
  {
    title: 'Pothole at Main Gate entrance road',
    summary: 'Large pothole on the main entrance road near the gate, causing vehicle damage and pedestrian hazard.',
    original_text: 'There is a big pothole at the Main Gate road. Cars are getting damaged and it is dangerous for everyone.',
    location: 'Main Gate',
    category: 'roads',
    urgency: 'medium',
    department: 'Estates & Maintenance (Roads & Grounds)',
    language: 'English',
    confidence: 'high',
    status: 'resolved',
    upvotes: 7,
    photo: null,
    ai_source: 'seed',
    created_at: iso(10),
    updated_at: iso(5),
    resolved_at: iso(5),
  },
  {
    title: 'Tap not working in Hostel A',
    summary: 'Water tap in Hostel A communal bathroom is broken and produces no water.',
    original_text: 'Maji haitoki kwa tap ya bathroom Hostel A. Tumekuwa tukitumia choo bila maji tangu jana.',
    location: 'Hostel A',
    category: 'water',
    urgency: 'high',
    department: 'Estates & Maintenance (Water & Plumbing)',
    language: 'Swahili',
    confidence: 'high',
    status: 'resolved',
    upvotes: 5,
    photo: null,
    ai_source: 'seed',
    created_at: iso(7),
    updated_at: iso(3),
    resolved_at: iso(3),
  },
];

const insert = db.prepare(`
  INSERT INTO issues (title, summary, original_text, location, category, urgency, department, language, confidence, status, upvotes, photo, ai_source, created_at, updated_at, resolved_at)
  VALUES (@title, @summary, @original_text, @location, @category, @urgency, @department, @language, @confidence, @status, @upvotes, @photo, @ai_source, @created_at, @updated_at, @resolved_at)
`);

const insertMany = db.transaction((rows) => {
  for (const row of rows) insert.run(row);
});

insertMany(issues);
console.log(`Seeded ${issues.length} issues.`);
