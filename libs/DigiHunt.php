<?php
class DigiHunt {
    private $url = 'https://www.digikala.com/treasure-hunt/products/';
    private $path = './images/';
    public  $products = [];

    public function __construct() {
        require_once('./libs/SimpleHtmlDom.php');
        require_once('./libs/Diff.php');
    }

    public function toRealImage($url = null) {
        $url = substr($url, 0, strpos($url, '?'));
        return $url;
    }

    public function fetchProducts($pages = 3, $allImages = false, $verbose = 0) {
        $html = new simple_html_dom();
        $images = [];

        if(is_null($pages)) {
            $html->load_file($this->url);
            $pages = $html->find('.c-pager__items .js-pagination__item', -1)->nodes[0]->attr['href'];
            $pages = (int) end(explode('&pageno=', $pages));
            if($verbose) echo "Product pages: $pages<br>";
        }
        
        for($i = 1; $i <= $pages; $i++) {
            if($verbose) echo 'در حال خزیدن در صفحه گنج #' . $i . '<br>';
            $html->load_file($this->url . '?sortby=4&pageno=' . $i);
            $ret = $html->find('.c-product-box img[src^=https://dkstatics-public.digikala.com/digikala-products/]');
            $images_counter = 0;
            
            foreach($ret as $img) {
                $originalURL = $img->attr['src'];
                if($verbose) $label = $img->attr['alt'];
                $realURL = $this->toRealImage($originalURL);
                $productID = explode('/', $img->parent->attr['href'])[2];
                $productLink = 'https://digikala.com/product/' . $productID;

                array_push($images, $realURL);

                if($allImages) {
                    $html->load_file($productLink);
                    $innerImages = $html->find('div.thumb-wrapper img');

                    foreach($innerImages as $img) {
                        // echo $this->toRealImage($img->attr['data-src']) . '<br>';
                        array_push($images, $this->toRealImage($img->attr['data-src']));
                    }
                }

                if($verbose) echo "<b><a href='$realURL'>$label</a></b><br>";
                ++$images_counter;
            }
        }

        return json_encode($images);
    }

    public function downloadImages($data = null) {
        $data = json_decode($data);

        foreach($data as $img) {
            $file_name = $this->path . basename($img);
            file_put_contents($file_name, file_get_contents($img));
        }

        return $this;
    }

    public function updateProducts($data = '', $as = 'last.json') {
        file_put_contents($as, $data);

        return $this;
    }

    public function compare($update = false, $pages = 2) {
        $last = file_get_contents('last.json');
        if($update) $newData = $this->updateProducts($this->fetchProducts($pages), 'new.json');
        echo Diff::toTable(Diff::compareFiles('last.json', 'new.json'));
        
        return $this;
    }

}
?>