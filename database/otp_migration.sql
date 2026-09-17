/* ==========================================================
   VOTIFY
   OTP Authentication Migration
   File : database/otp_migration.sql
========================================================== */

USE votify;


/* ==========================================================
   ADD EMAIL VERIFIED
========================================================== */

ALTER TABLE students
ADD COLUMN email_verified
TINYINT(1)
NOT NULL
DEFAULT 0
AFTER college_email;


/* ==========================================================
   FIX ACCOUNT STATUS

   Existing PHP admin code already checks:
   Pending
   Approved
   Rejected
========================================================== */

ALTER TABLE students
MODIFY COLUMN status
ENUM(
    'Pending',
    'Approved',
    'Rejected'
)
NOT NULL
DEFAULT 'Pending';


/* ==========================================================
   MAKE SURE VOTE STATUS EXISTS

   NOTE:
   If vote_status already exists in your current DB,
   DO NOT run this ALTER again.
========================================================== */

/*
ALTER TABLE students
ADD COLUMN vote_status
ENUM(
    'Voted',
    'Unvoted'
)
NOT NULL
DEFAULT 'Unvoted';
*/


/* ==========================================================
   OPTIONAL:
   CHECK RESULT
========================================================== */

DESCRIBE students;