SELECT * FROM head;
SELECT * FROM fammem;
SELECT * FROM evac;

ALTER TABLE fammem ADD COLUMN Head_of_Fam_ID INT;

UPDATE fammem f
SET Head_of_Fam_ID = (
    SELECT h.Head_of_Fam_ID
    FROM head h
    WHERE h.Family_Member_ID = f.Family_Member_ID  -- Match using fam_id
);

ALTER TABLE fammem MODIFY COLUMN head_id INT NOT NULL;

ALTER TABLE fam_table 
ADD CONSTRAINT fk_fam_head 
FOREIGN KEY (head_id) REFERENCES head_table(head_id);

ALTER TABLE fam_table DROP PRIMARY KEY;
ALTER TABLE fam_table ADD PRIMARY KEY (fam_id, head_id);

DELETE FROM head
WHERE Head_of_Fam_ID BETWEEN 1 AND 6;

DELETE FROM fammem
WHERE Family_Member_ID BETWEEN 1177 AND 5598;


