CREATE TABLE tickets (
 id INTEGER PRIMARY KEY AUTOINCREMENT, subject TEXT NOT NULL, requester TEXT NOT NULL,
 email TEXT NOT NULL, description TEXT NOT NULL, priority TEXT NOT NULL CHECK(priority IN ('normal','high','urgent')),
 status TEXT NOT NULL DEFAULT 'open' CHECK(status IN ('open','active','waiting','resolved')),
 owner TEXT NOT NULL DEFAULT 'Unassigned', version INTEGER NOT NULL DEFAULT 1,
 created TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP, updated TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE activity (id INTEGER PRIMARY KEY AUTOINCREMENT,ticket_id INTEGER NOT NULL REFERENCES tickets(id),kind TEXT NOT NULL,body TEXT NOT NULL,created TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP);
CREATE INDEX ticket_status ON tickets(status,priority);
CREATE INDEX activity_ticket ON activity(ticket_id,id);
CREATE TRIGGER ticket_created AFTER INSERT ON tickets BEGIN
 INSERT INTO activity(ticket_id,kind,body) VALUES(NEW.id,'Created','Ticket opened by ' || NEW.requester);
END;
CREATE TRIGGER ticket_changed AFTER UPDATE ON tickets BEGIN
 INSERT INTO activity(ticket_id,kind,body) VALUES(NEW.id,'Updated',OLD.status || ' → ' || NEW.status || '; owner: ' || NEW.owner || '; priority: ' || NEW.priority);
END;
INSERT INTO tickets(subject,requester,email,description,priority,status,owner) VALUES
('Studio printer stops halfway through a job','Nora de Vries','nora@example.test','A3 jobs stop after the first page. A4 prints normally. Please check the queue before the afternoon workshop.','urgent','open','Unassigned'),
('Access to the shared project archive','Sam Patel','sam@example.test','I can see the project folder but cannot open the 2025 archive. My team is Research.','normal','active','Alex'),
('Meeting room display loses its signal','Mina Vermeer','mina@example.test','The display goes black after about ten minutes. Reconnecting the cable restores it.','high','waiting','Robin'),
('New starter equipment checklist','Leo Janssen','leo@example.test','Please prepare a laptop, dock and headset for the new starter next Monday.','normal','open','Unassigned'),
('Update the studio Wi-Fi instructions','Iris Bakker','iris@example.test','The printed guest-network instructions show the old network name.','normal','resolved','Alex');
INSERT INTO activity(ticket_id,kind,body) VALUES(3,'Note','Replacement cable ordered. Waiting for delivery.'),(2,'Note','Access request confirmed with the project owner.');
