CREATE TABLE `application_token` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `scope_id` int(11) NOT NULL,
  `token` char(64) NOT NULL,
  `refresh_token` char(64) NOT NULL,
  `expires_in` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;