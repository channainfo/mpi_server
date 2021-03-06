ALTER TABLE `mpi_patient`
      ADD `visits_count` INT(10) NOT NULL AFTER `fingerprint_l5`,
      ADD `visit_positives_count` INT(10) NOT NULL AFTER `visits_count`,
      ADD `created_at` DATETIME NOT NULL AFTER `visit_positives_count`,
      ADD `updated_at` DATETIME NOT NULL
      AFTER `created_at`;


ALTER TABLE `mpi_patient` CHANGE `visits_count` `visits_count` INT(10) NOT NULL DEFAULT '0';
ALTER TABLE `mpi_patient` CHANGE `visit_positives_count` `visit_positives_count` INT(10) NOT NULL DEFAULT '0';
