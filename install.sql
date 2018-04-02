CREATE TABLE `products` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `price` decimal(6,2) NOT NULL DEFAULT '0.00',
 PRIMARY KEY (`id`)
);

CREATE PROCEDURE `InsertRand`(IN NumRows INT, IN MinVal INT, IN MaxVal INT)
BEGIN
    DECLARE i INT;
    DECLARE price INT;
    SET i = 1;
    START TRANSACTION;
    WHILE i <= NumRows DO
        SET price = MinVal + CEIL(RAND() * (MaxVal - MinVal));
        INSERT INTO products(name, price) VALUES ('product', price);
        SET i = i + 1;
    END WHILE;
    COMMIT;
END;

CREATE PROCEDURE `getRandomProduct`(IN `maxPrice` DECIMAL(8,2), OUT `productId` INT, OUT `productPrice` DECIMAL(8,2))
BEGIN
   SET productId = 0;
       SELECT id, price INTO productId, productPrice
       FROM products
       WHERE price < maxPrice
       ORDER BY RAND()
       LIMIT 1;
END;

CREATE PROCEDURE `findProductWithExactPrice`(IN `exactPrice` DECIMAL(8,2), OUT `productId` INT(11))
    READS SQL DATA
BEGIN
  SET productId = 0;
  SELECT id INTO productId 
  FROM products 
  WHERE price = exactPrice
  ORDER BY RAND()
  LIMIT 1;
END;

CREATE PROCEDURE `findProductClosestToPrice`(IN `priceLimit` DECIMAL(8,2), OUT `productId` INT(11))
    READS SQL DATA
BEGIN
	SET productId = 0;
	SELECT id INTO productId 
	FROM products 
	WHERE price <= priceLimit
	ORDER BY RAND()
	LIMIT 1;
END;

CREATE PROCEDURE `get30products`(OUT `str` TEXT, OUT `remainingPrice` DECIMAL(8,2))
BEGIN
  DECLARE x INT;
  DECLARE id INT;
  DECLARE price DECIMAL(8,2);
  SET x = 30;
  SET str = '';
  SET remainingPrice = 500.00;
  REPEAT
    CALL getRandomProduct(remainingPrice/x, @id, @price);
    SET str = CONCAT(str,',', @id);
    SET x = x - 1;
    SET remainingPrice = remainingPrice - @price;
    UNTIL x <= 1
  END REPEAT;
  SET str = SUBSTRING(str, 2);
END;
