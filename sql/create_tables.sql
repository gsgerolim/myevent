-- PostgreSQL DDL for Hotsite Evento
CREATE TABLE IF NOT EXISTS admins (
  id SERIAL PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS events (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  responsible VARCHAR(255),
  summary TEXT,
  address TEXT,
  latitude NUMERIC,
  longitude NUMERIC,
  date_start TIMESTAMP,
  date_end TIMESTAMP,
  capacity INTEGER,
  unlimited BOOLEAN DEFAULT FALSE,
  image VARCHAR(255),
  status VARCHAR(20) DEFAULT 'active',
  category VARCHAR(100),
  cost VARCHAR(50),
  link VARCHAR(255),
  created_at TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS attendees (
  id SERIAL PRIMARY KEY,
  event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
  name VARCHAR(255),
  profile VARCHAR(50),
  phone VARCHAR(50),
  email VARCHAR(255),
  cpf VARCHAR(50),
  notes TEXT,
  status VARCHAR(20) DEFAULT 'confirmed', -- confirmed, waitlist, excess
  created_at TIMESTAMP DEFAULT now()
);

CREATE TABLE IF NOT EXISTS ads (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255),
  image VARCHAR(255),
  link VARCHAR(255),
  date_start DATE,
  date_end DATE,
  display_time INTEGER DEFAULT 5,
  status VARCHAR(20) DEFAULT 'active',
  created_at TIMESTAMP DEFAULT now()
);

