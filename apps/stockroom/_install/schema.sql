CREATE TABLE items(id INTEGER PRIMARY KEY AUTOINCREMENT,sku TEXT NOT NULL UNIQUE,name TEXT NOT NULL,category TEXT NOT NULL,location TEXT NOT NULL,supplier TEXT NOT NULL,reorder_level INTEGER NOT NULL DEFAULT 5 CHECK(reorder_level >= 0),stock INTEGER NOT NULL DEFAULT 0 CHECK(stock >= 0),unit_cost INTEGER NOT NULL DEFAULT 0 CHECK(unit_cost >= 0),version INTEGER NOT NULL DEFAULT 1);
CREATE TABLE movements(id INTEGER PRIMARY KEY AUTOINCREMENT,item_id INTEGER NOT NULL REFERENCES items(id),delta INTEGER NOT NULL CHECK(delta <> 0),note TEXT NOT NULL,request_key TEXT NOT NULL UNIQUE,created TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP);
CREATE INDEX movement_item ON movements(item_id,id);
CREATE TRIGGER stock_moved AFTER INSERT ON movements BEGIN UPDATE items SET stock=stock+NEW.delta WHERE id=NEW.item_id; END;
INSERT INTO items(sku,name,category,location,supplier,reorder_level,unit_cost) VALUES
('PAPER-A4','A4 recycled paper','Office','A-01','Paper & Co',10,650),
('USB-C-2M','USB-C cable, 2 metres','IT','B-03','Cable House',8,1295),
('MARKER-BK','Black whiteboard marker','Workshop','A-04','Paper & Co',12,195),
('TAPE-50','Packing tape, 50 mm','Shipping','C-02','Box Works',6,375),
('MAILER-M','Padded mailer, medium','Shipping','C-01','Box Works',20,85),
('LABEL-24','A4 label sheets, 24 per page','Office','A-02','Paper & Co',5,1495);
INSERT INTO movements(item_id,delta,note,request_key) VALUES
(1,24,'Opening stock','seed-1'),(2,5,'Opening stock','seed-2'),(3,9,'Opening stock','seed-3'),(4,18,'Opening stock','seed-4'),(5,45,'Opening stock','seed-5'),(6,3,'Opening stock','seed-6');
