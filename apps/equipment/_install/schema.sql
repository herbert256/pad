CREATE TABLE resources(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL UNIQUE,category TEXT NOT NULL,location TEXT NOT NULL,description TEXT NOT NULL);
CREATE TABLE bookings(id INTEGER PRIMARY KEY AUTOINCREMENT,resource_id INTEGER NOT NULL REFERENCES resources(id),borrower TEXT NOT NULL,email TEXT NOT NULL,purpose TEXT NOT NULL,start_day TEXT NOT NULL,end_day TEXT NOT NULL,status TEXT NOT NULL DEFAULT 'reserved' CHECK(status IN ('reserved','collected','returned','cancelled')),created TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,CHECK(end_day > start_day));
CREATE INDEX booking_dates ON bookings(resource_id,start_day,end_day,status);
CREATE TABLE history(id INTEGER PRIMARY KEY AUTOINCREMENT,booking_id INTEGER NOT NULL REFERENCES bookings(id),event TEXT NOT NULL,created TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP);
CREATE TRIGGER booked AFTER INSERT ON bookings BEGIN INSERT INTO history(booking_id,event) VALUES(NEW.id,'Reservation created'); END;
CREATE TRIGGER booking_changed AFTER UPDATE OF status ON bookings BEGIN INSERT INTO history(booking_id,event) VALUES(NEW.id,OLD.status || ' → ' || NEW.status); END;
INSERT INTO resources(name,category,location,description) VALUES
('Cinema camera A','Video','Studio shelf 2','Camera body, two batteries, charger and a 24–70 mm lens.'),
('Podcast kit','Audio','Sound cupboard','Two microphones, headphones, USB interface and cables.'),
('Portable projector','Presentation','Meeting room store','HDMI projector with remote, stand and travel case.'),
('Lighting pair','Video','Studio shelf 4','Two LED panels, power supplies and lightweight stands.'),
('Field recorder','Audio','Sound cupboard','Portable recorder, wind shield and spare batteries.'),
('Workshop laptop','Computing','IT locker','Laptop, charger and USB-C presentation adapter.');
INSERT INTO bookings(resource_id,borrower,email,purpose,start_day,end_day,status) VALUES
(1,'Nora de Vries','nora@example.test','Interview with the design team',date('now','+1 day'),date('now','+3 days'),'reserved'),
(2,'Sam Patel','sam@example.test','Monthly research podcast',date('now'),date('now','+2 days'),'collected'),
(3,'Mina Vermeer','mina@example.test','Client workshop',date('now','+4 days'),date('now','+5 days'),'reserved'),
(5,'Leo Janssen','leo@example.test','Field recording session',date('now','-3 days'),date('now','-1 day'),'returned');
