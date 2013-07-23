ALTER TABLE  `store_products_reviews` ADD  `userid` INT( 11 ) NOT NULL AFTER  `products_review_id` ,
ADD  `user_rating` INT( 11 ) NOT NULL AFTER  `userid`