<?php


class Product
{

    const SHOW_BY_DEFAULT = 8;

    const SHOW_BY_DEFAULT_PRODUCT = 16;

    /**
     * Returns an array of categories
     */
    public static function getLatestProducts($count = self::SHOW_BY_DEFAULT)
    {
        $count = intval($count);

        $db = Db::getConnection();

        $productsList = array();

        $sql = "select id, name, price, is_new from product where status = 1 order by RAND() desc limit " . $count;

        $result = $db->prepare($sql);

        $result->execute();

        $i = 0;
        while ($row = $result->fetch()) {
            $productsList[$i]['id'] = $row['id'];
            $productsList[$i]['name'] = $row['name'];
            $productsList[$i]['price'] = $row['price'];
            // $productsList[$i]['image'] = $row['image'];
            $productsList[$i]['is_new'] = $row['is_new'];
            $i++;
        }

        // echo var_dump($categoryList);

        return $productsList;
    }

    public static function addProduct($options)
    {
        $db = DB::getConnection();

        $sql = "insert into product (name,code,price,category_id,brand,availability,description,is_new,is_recommended,status) values (:name,:code,:price,:category_id,:brand,:availability,:description,:is_new,:is_recommended,:status)";

        $result = $db->prepare($sql);

        $result->bindParam(":name", $options["name"], PDO::PARAM_STR);
        $result->bindParam(":code", $options["code"], PDO::PARAM_STR);
        $result->bindParam(":price", $options["price"], PDO::PARAM_STR);
        $result->bindParam(":category_id", $options["category_id"], PDO::PARAM_STR);
        $result->bindParam(":brand", $options["brand"], PDO::PARAM_STR);
        $result->bindParam(":availability", $options["availability"], PDO::PARAM_STR);
        $result->bindParam(":description", $options["description"], PDO::PARAM_STR);
        $result->bindParam(":is_new", $options["is_new"], PDO::PARAM_STR);
        $result->bindParam(":is_recommended", $options["is_recommended"], PDO::PARAM_STR);
        $result->bindParam(":status", $options["status"], PDO::PARAM_STR);

        if ($result->execute()) {
            return $db->lastInsertId();
        }

        return 0;
    }

    public static function getProductsList($page = 1)
    {
        $db = DB::getConnection();

        $offset = ($page - 1) * Product::SHOW_BY_DEFAULT_PRODUCT;

        $productsList = array();

        $sql = "select id, code, name, price from product limit " . Order::SHOW_BY_DEFAULT . " 
        offset $offset;";

        $result = $db->prepare($sql);
        $result->execute();

        $i = 0;
        while ($row = $result->fetch()) {
            $productsList[$i]['id'] = $row['id'];
            $productsList[$i]['code'] = $row['code'];
            $productsList[$i]['name'] = $row['name'];
            $productsList[$i]['price'] = $row['price'];
            $i++;
        }
        return $productsList;
    }

    public static function deleteProductById($id)
    {
        $db = DB::getConnection();

        $sql = "delete from product where id = :id";

        $result = $db->prepare($sql);
        $result->bindParam(":id", $id, PDO::PARAM_STR);
        // error_log("Продукт удален $id");
        $result->execute();
    }

    public static function updateProductById($options)
    {
        $db = DB::getConnection();

        $sql = "update product set name = :name, code = :code, price = :price, category_id = :category_id, brand = :brand, availability = :availability, description = :description, is_new = :is_new, is_recommended = :is_recommended, status = :status where id = :id";

        $result = $db->prepare($sql);

        $result->bindParam(":id", $options["id"], PDO::PARAM_STR);
        $result->bindParam(":name", $options["name"], PDO::PARAM_STR);
        $result->bindParam(":code", $options["code"], PDO::PARAM_STR);
        $result->bindParam(":price", $options["price"], PDO::PARAM_STR);
        $result->bindParam(":category_id", $options["category_id"], PDO::PARAM_STR);
        $result->bindParam(":brand", $options["brand"], PDO::PARAM_STR);
        $result->bindParam(":availability", $options["availability"], PDO::PARAM_STR);
        $result->bindParam(":description", $options["description"], PDO::PARAM_STR);
        $result->bindParam(":is_new", $options["is_new"], PDO::PARAM_STR);
        $result->bindParam(":is_recommended", $options["is_recommended"], PDO::PARAM_STR);
        $result->bindParam(":status", $options["status"], PDO::PARAM_STR);


        if (!$result->execute()) {
            $error = $result->errorInfo();
        }

        return Product::getProductById($options["id"]);
    }

    public static function getRecomendedProducts()
    {

        $db = DB::getConnection();

        $productsList = array();

        $sql = "select id, name, price, is_recommended from product where is_recommended = 1";

        $result = $db->prepare($sql);
        $result->execute();

        $i = 0;
        while ($row = $result->fetch()) {
            $productsList[$i]['id'] = $row['id'];
            $productsList[$i]['name'] = $row['name'];
            $productsList[$i]['price'] = $row['price'];
            // $productsList[$i]['image'] = $row['image'];
            $productsList[$i]['is_recommended'] = $row['is_recommended'];
            $i++;
        }
        return $productsList;
    }
    

    /**
     * Returns an array of products for category
     */
    public static function getProductsListByCategory($sort_op, $categoryId = false, $page = 1)
    {
        if ($categoryId) {
            $page  = intval($page);
            $offset = ($page - 1) * self::SHOW_BY_DEFAULT;

            $db = Db::getConnection();
            // $sql = "select id, name, price, is_new from product where status = 1 and category_id = :categoryId order by $sort_op limit " . self::SHOW_BY_DEFAULT . " offset $offset";

            // Запрос возвращает спискок продуктов со скидками
            $sql = "select pr.id, pr.name, pr.price, pr.is_new,
            prod.date_start pr_date_start, prod.date_end pr_date_end, prod.item_id pr_item_id, prod.item_type pr_item_type, prod.discount pr_discount,
            proc.date_start cat_date_start, proc.date_end cat_date_end, proc.item_id cat_item_id, proc.item_type cat_item_type, proc.discount cat_discount
            from product pr
            LEFT JOIN promotions_and_discounts prod
            ON pr.id = prod.item_id AND prod.item_type = 'P' AND prod.date_start <= NOW()  and prod.date_end >= NOW()
            LEFT JOIN promotions_and_discounts proc
            ON pr.category_id = proc.item_id AND proc.item_type = 'C' AND proc.date_start <= NOW()  and proc.date_end >= NOW() 
            where pr.status = 1 and category_id = :categoryId 
            order by $sort_op 
            limit " . self::SHOW_BY_DEFAULT . " 
            offset $offset;";

            $result = $db->prepare($sql);
            $result->bindParam(":categoryId", $categoryId, PDO::PARAM_STR);
            $result->execute();

            $products = array();
            $i = 0;
            while ($row = $result->fetch()) {
                $product = $row;
                $product['discount_price'] = false;
                Product::fillDiscountInfo($product);
                array_push($products, $product);
            }
            return $products;
        }
    }

    public static function getProductImage($productId)
    {
        $image = $_SERVER["DOCUMENT_ROOT"] . UPLOAD_IMAGE_PATH . "{$productId}.jpg";

        if (file_exists($image)) {
            return UPLOAD_IMAGE_PATH . "{$productId}.jpg";
        }
        return UPLOAD_IMAGE_PATH . "no_image.jpg";
    }

    /**
     * Returns single product by id
     */
    public static function getProduct($productId)
    {
        if ($productId) {
            $db = Db::getConnection();          

            $sql = "select pr.id, pr.name, pr.code, pr.availability, pr.brand, pr.description, pr.price, pr.recipe, pr.is_new,
            prod.date_start pr_date_start, prod.date_end pr_date_end, prod.item_id pr_item_id, prod.item_type pr_item_type, prod.discount pr_discount,
            proc.date_start cat_date_start, proc.date_end cat_date_end, proc.item_id cat_item_id, proc.item_type cat_item_type, proc.discount cat_discount
            from product pr
            LEFT JOIN promotions_and_discounts prod
            ON pr.id = prod.item_id AND prod.item_type = 'P' AND prod.date_start <= NOW()  and prod.date_end >= NOW()
            LEFT JOIN promotions_and_discounts proc
            ON pr.category_id = proc.item_id AND proc.item_type = 'C' AND proc.date_start <= NOW()  and proc.date_end >= NOW() 
            where pr.status = 1 and pr.id = :productId";

            $result = $db->prepare($sql);
            $result->bindParam(":productId", $productId, PDO::PARAM_STR);
            $result->execute();

            $product = array();        

            $i = 0;
            while ($row = $result->fetch()) {
                $product = $row;
                $product['discount_price'] = false;
                Product::fillDiscountInfo($product);
                array_push($product, $product);
            }        

            return $product;
        }
    }

    public static function getProductById($productId)
    {
        if ($productId) {
            $db = Db::getConnection();


            $sql = "select * from product where id = :productId";

            $result = $db->prepare($sql);

            $result->bindParam(":productId", $productId, PDO::PARAM_STR);

            $result->execute();
            //$result = $db->query("select id, name, price, is_new, availability, brand, code from product where status = 1 and id = '$productId'");
            // echo var_dump($result);            
            $product = $result->fetch();

            //echo var_dump($product);


            return $product;
        }
    }

    /**
     * Returns count of a products in category
     */
    public static function getTotalProductsInCategory($categoryId)
    {
        $db = Db::getConnection();

        $sql = "select count(id) as count from product where status = 1 and category_id = :categoryId";

        $result = $db->prepare($sql);

        $result->bindParam(":categoryId", $categoryId, PDO::PARAM_STR);

        $result->execute();

        //$result = $db->query("select count(id) as count from product where status = 1 and category_id = '$categoryId'");
        // echo var_dump($result);            
        //$result->setFetchMode(PDO::FETCH_ASSOC);
        $row = $result->fetch();

        //echo var_dump($row);

        return $row["count"];
    }

    public static function getProductsByIds($idsArray)
    {
        $products = array();

        if (count($idsArray) == 0) {
            return $products;
        }

        $db = DB::getConnection();

        $idString = implode(',', $idsArray);

        // $sql = "select * from product where status = '1' and id in ($idString)";

        $sql = "select pr.id, pr.name, pr.code, pr.price, pr.is_new,
        prod.date_start pr_date_start, prod.date_end pr_date_end, prod.item_id pr_item_id, prod.item_type pr_item_type, prod.discount pr_discount,
        proc.date_start cat_date_start, proc.date_end cat_date_end, proc.item_id cat_item_id, proc.item_type cat_item_type, proc.discount cat_discount
        from product pr
        LEFT JOIN promotions_and_discounts prod
        ON pr.id = prod.item_id AND prod.item_type = 'P' AND prod.date_start <= NOW()  and prod.date_end >= NOW()
        LEFT JOIN promotions_and_discounts proc
        ON pr.category_id = proc.item_id AND proc.item_type = 'C' AND proc.date_start <= NOW()  and proc.date_end >= NOW() 
        where pr.status = 1 and pr.id in ($idString)";

        $result = $db->prepare($sql);
        // $result->bindParam(":idString", $idString, PDO::PARAM_STR);
        $result->execute();

        $products = array();
            $i = 0;
            while ($row = $result->fetch()) {
                $product = $row;
                $product['discount_price'] = false;
                Product::fillDiscountInfo($product);
                array_push($products, $product);
            }
            // return $products;

        // $result->bindParam(":idString", $idString, PDO::PARAM_STR);

        // $result = $db->query($sql);

        // $result->setFetchMode(PDO::FETCH_ASSOC);

        /*$i = 0;
        while ($row = $result->fetch()) {
            $products[$i]['id'] = $row['id'];
            $products[$i]['name'] = $row['name'];
            $products[$i]['price'] = $row['price'];
            $products[$i]['code'] = $row['code'];
            $i++;
        }*/        

        return $products;
    }

    // Поиск 
    public static function Search($searchString)
    {
        $db = DB::getConnection();

        $searchString = str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $searchString);

        $likeString = "%{$searchString}%";

        $sql = "select * from product where status = '1' and name like :search";
        $result = $db->prepare($sql);
        $result->bindParam(":search", $likeString, PDO::PARAM_STR);
        $result->execute();

        $products = array();
        $i = 0;
        while ($row = $result->fetch()) {
            $products[$i]['id'] = $row['id'];
            $products[$i]['name'] = $row['name'];
            $products[$i]['price'] = $row['price'];
            $products[$i]['code'] = $row['code'];
            $products[$i]['is_new'] = $row['is_new'];
            $i++;
        }
        return $products;
    }

    public static function searchAdmin($searchString, $page = 1, $sql)
    {
        $db = DB::getConnection();

        $offset = ($page - 1) * Product::SHOW_BY_DEFAULT;
        $limit = Product::SHOW_BY_DEFAULT;

        $searchString = str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $searchString);;

        $likeString = "%{$searchString}%";

        $result = $db->prepare($sql);
        $result->bindParam(":search", $likeString, PDO::PARAM_STR);
        $result->bindParam(":limit", $limit, PDO::PARAM_INT);
        $result->bindParam(":offset", $offset, PDO::PARAM_INT);
        $result->execute();

        $recipes = array();
        $i = 0;
        while ($row = $result->fetch()) {
            $recipes[$i]['id'] = $row['id'];
            $recipes[$i]['user_name'] = $row['user_name'];
            $recipes[$i]['user_email'] = $row['user_email'];
            $recipes[$i]['date_created'] = $row['date_created'];
            $recipes[$i]['status'] = $row['status'];
            $i++;
        }
        return $recipes;
    }

    /* public static function checkProductDiscount($productId)
    {

        $items = self::getProductDiscount($productId, 'P');

        if (count($items) <= 0) {
            $items = self::getProductDiscount($productId, 'C');
        }

        if (count($items) > 0) {
            return $items[0];
        }

        return false;
    }

    public static function getProductDiscount($productId, $itemType)
    {

        $products = array();
        $db = Db::getConnection();

        $sql = "SELECT pad.discount, pad.date_end 
        from product as pr
        left join promotions_and_discounts as pad
        on pr.id = pad.item_id
        where pr.id = :productId and pad.item_type = :itemType and pad.date_start <= NOW()  and pad.date_end >= NOW();";

        $result = $db->prepare($sql);

        $result->bindParam(":productId", $productId, PDO::PARAM_INT);
        $result->bindParam(":itemType", $itemType, PDO::PARAM_STR_CHAR);

        $result->execute();

        $result->setFetchMode(PDO::FETCH_ASSOC);

        $discounts = $result->fetchAll();

        return $discounts;
    }*/

    public static function getDiscountList($page = 1)
    {
        $db = Db::getConnection();

        $offset = ($page - 1) * Product::SHOW_BY_DEFAULT;

        $sql = "SELECT *,
        case 
            when prom.item_type = 'P' then (
                SELECT NAME 
                FROM product AS pr
                WHERE prom.item_id = pr.id
            )
            ELSE (
                SELECT name
                FROM category cat
                WHERE prom.item_id = cat.id
            )
        END AS item_name
        from promotions_and_discounts as prom
        limit " . Product::SHOW_BY_DEFAULT . " 
        offset $offset;";

        $result = $db->prepare($sql);
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $discountsList = array();
        $discountsList = $result->fetchAll();

        return $discountsList;
    }

    public static function addDiscount($options)
    {
        $db = Db::getConnection();

        $sql = "insert into promotions_and_discounts (name,date_start,date_end,item_type,item_id,discount) values (:name,:date_start,:date_end,:item_type,:item_id,:discount)";

        $result = $db->prepare($sql);
        $result->bindParam(":name", $options["name"], PDO::PARAM_STR);
        $result->bindParam(":date_start", $options["date_start"], PDO::PARAM_STR);
        $result->bindParam(":date_end", $options["date_end"], PDO::PARAM_STR);
        $result->bindParam(":item_type", $options["item_type"], PDO::PARAM_STR_CHAR);
        $result->bindParam(":item_id", $options["item_id"], PDO::PARAM_INT);
        $result->bindParam(":discount", $options["discount"], PDO::PARAM_INT);

        $result->execute();

        return true;
    }

    public static function deleteDiscountById($id)
    {
        $db = DB::getConnection();

        $sql = "delete from promotions_and_discounts where id = :id";

        $result = $db->prepare($sql);
        $result->bindParam(":id", $id, PDO::PARAM_STR);

        $result->execute();

        return true;
    }

    public static function getDiscountById($id)
    {
        $db = DB::getConnection();

        $sql = "select * from promotions_and_discounts where id = :id;";

        $result = $db->prepare($sql);
        $result->bindParam(":id", $id, PDO::PARAM_INT);

        $result->execute();

        $discount = $result->fetch();

        return $discount;
    }

    public static function updateDiscount($options)
    {
        $db = DB::getConnection();

        $sql = "update promotions_and_discounts set name = :name, date_start = :date_start, date_end = :date_end, item_id = :item_id, item_type = :item_type, discount = :discount where id = :id";

        $result = $db->prepare($sql);

        $result->bindParam(":id", $options["id"], PDO::PARAM_STR);
        $result->bindParam(":name", $options["name"], PDO::PARAM_STR);
        $result->bindParam(":date_start", $options["date_start"], PDO::PARAM_STR);
        $result->bindParam(":date_end", $options["date_end"], PDO::PARAM_STR);
        $result->bindParam(":item_id", $options["item_id"], PDO::PARAM_INT);
        $result->bindParam(":item_type", $options["item_type"], PDO::PARAM_STR_CHAR);
        $result->bindParam(":discount", $options["discount"], PDO::PARAM_INT);

        $result->execute();

        return true;
    }

    public static function getTotalDiscounts()
    {
        $db = DB::getConnection();

        $sql = "select count(*) as count from promotions_and_discounts order by id desc;";

        $result = $db->prepare($sql);

        $result->execute();

        $row = $result->fetch();

        return $row["count"];
    }

    public static function getProductsWithDiscounts($page = 1)
    {
        $db = DB::getConnection();

        $offset = ($page - 1) * Product::SHOW_BY_DEFAULT;

        $sql = "select pr.id, pr.name, pr.price, pr.is_new,
        prod.date_start pr_date_start, prod.date_end pr_date_end, prod.item_id pr_item_id, prod.item_type pr_item_type, prod.discount pr_discount,
        proc.date_start cat_date_start, proc.date_end cat_date_end, proc.item_id cat_item_id, proc.item_type cat_item_type, proc.discount cat_discount
        from product pr
        LEFT JOIN promotions_and_discounts prod
        ON pr.id = prod.item_id AND prod.item_type = 'P' AND prod.date_start <= NOW()  and prod.date_end >= NOW()
        LEFT JOIN promotions_and_discounts proc
        ON pr.category_id = proc.item_id AND proc.item_type = 'C' AND proc.date_start <= NOW()  and proc.date_end >= NOW() 
        where pr.status = 1 and (prod.id is not null or proc.id is not null)
        limit " . self::SHOW_BY_DEFAULT . " 
        offset $offset;";

        $result = $db->prepare($sql);

        $result->execute();
        $products = array();

        /**
         * TODO: Разобраться как правильно это сделать, а не как ниже
         */
        $products = $result->fetchAll();
        foreach ($products as &$product) {
            Product::fillDiscountInfo($product);
        }

        /*while ($row = $result->fetch()) {
            $product = $row;                
            $product['discount_price'] = false;
            Product::fillDiscountInfo($product);
            array_push($products, $product);
        }*/

        return $products;
    }

    public static function getTotalProducts()
    {

        $db = DB::getConnection();

        $sql = "select count(*) as count from product order by id desc;";

        $result = $db->prepare($sql);

        $result->execute();

        $row = $result->fetch();

        return $row["count"];
    }

    public static function fillDiscountInfo(&$product)
    {

        if (!is_null($product["pr_discount"])) {
            $product['discount_price'] =  $product['price'] - (($product['price'] / 100) * $product['pr_discount']);
            $product['discount_date_end'] = $product['pr_date_end'];
            $product['discount'] = $product['pr_discount'];
        } elseif (!is_null($product["cat_discount"])) {
            $product['discount_price'] =  $product['price'] - (($product['price'] / 100) * $product['cat_discount']);
            $product['discount_date_end'] = $product['cat_date_end'];
            $product['discount'] = $product['cat_discount'];
        }

        return $product;
    }

    public static function getRecommendedProductByCategory($idProduct) {

        $recProducts = array();

        $db = DB::getConnection();

        $sql = "SELECT * FROM product WHERE category_id = (SELECT category_id FROM product WHERE id = :idProduct)";

        $result = $db->prepare($sql);

        $result->bindParam(":idProduct", $idProduct, PDO::PARAM_STR);

        $result->execute();

        $recProducts = $result->fetchAll();

        return $recProducts;
    }

    public static function getOrganiChuvstv() {

        $organiChuvstv = array();
        $db = DB::getConnection();

        $sql = "select id, name, price from product where category_id = 13";

        $result = $db->prepare($sql);

        $result->execute();

        $organiChuvstv = $result->fetchAll();

        return $organiChuvstv;
    }

    public static function getObezbPrep() {

        $obezbPrep = array();
        $db = DB::getConnection();

        $sql = "select id, name, price
        from product
        where category_id = 15";

        $result = $db->prepare($sql);

        $result->execute();

        $obezbPrep = $result->fetchAll();

        return $obezbPrep;
    }
    
}
