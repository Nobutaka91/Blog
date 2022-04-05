<?php 
class QueryArticle extends Connect {

    private $article;

    const THUMBS_WIDTH = 200; // サムネイルの幅

    public function __construct(){
        parent::__construct();
    }


    public function setArticle(Article $article){
        $this->article = $article;
    }

    //画像アップロード
    private function saveFile($old_name){
        
        // 画像保存処理
        if ($file = $this->article->getFile()){
            $old_name = $file['tmp_name'];
            // date関数は日付の書式化をする関数
            $new_name = date('YmdHis').mt_rand();

            // アップロード可否を決める変数。デフォルトはアップロード不可
            $is_upload = false;

            // 画像の種類を取得する
            $type = exif_imagetype($old_name);
            // ファイルの種類が画像だったとき、種類によって拡張子を変更
            switch ($type){
                case IMAGETYPE_JPEG:
                    $new_name .= '.jpeg';
                    $is_upload = true;
                    break;
                case IMAGETYPE_GIF:
                    $new_name .= '.gif';
                    $is_upload = true;
                    break;
                case IMAGETYPE_PNG:
                    $new_name .= '.png';
                    $is_upload = true;
                    break;
            }

            if ($is_upload && move_uploaded_file($old_name, __DIR__ . '/../album/'.$new_name)){
                $this->article->setFilename($new_name);
                $filename = $this->article->getFilename();
            }
        }

        $new_name = date('YmdHis') . mt_rand();


        // echo '<pre>';
        // var_dump($old_name);
        // var_dump(exif_imagetype($old_name));
        // exit();


        if ($type = exif_imagetype($old_name)){
            // 元画像の縦横サイズを取得
            list($width, $height) = getimagesize($old_name);

            // サムネイルの比率を求める
            $rate = self::THUMBS_WIDTH / $width; // 比率
            $thumbs_height = $rate * $height;

            /**
             * キャンパス作成
             * @param int self::THUMBS_WIDTH 画像の幅
             * @param int $thumbs_height 画像の高さ
             */
            $canvas = imagecreatetruecolor(self::THUMBS_WIDTH, $thumbs_height);

            switch($type){
                case IMAGETYPE_JPEG:
                    $new_name .= '.jpg'; // 新ファイル名の末尾に拡張子「.jpg」をつける
                    //サムネイルを保存
                    $image = imagecreatefromjpeg($old_name); // 元画像の画像リソースを取得して$imageに代入
                    /**
                     * 元画像を再サンプリングしてコピーする
                     *
                     * @param int $canvas ・・・・・ キャンバスの画像リソース
                     * @param int $image ・・・・・・コピー元の画像リソース
                     * 0,0,0,0 ・・・・・・・・・・・コピー先のx座標, y座標, コピー元のx座標, y座標
                     * @param self::THUMBS_WIDTH ・・コピー先の幅
                     * @param int $thumbs_height ・・コピー先の高さ
                     * @param int $width ・・・・・・コピー元の幅
                     * @param int $height・・・・・・コピー元の高さ
                     */
                    imagecopyresampled($canvas, $image, 0,0,0,0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
                    imagejpeg($canvas, __DIR__.'/../album/thumbs-'.$new_name); // 1.出力したい画像リソース、2.ファイルの保存先
                    break;

                case IMAGETYPE_GIF:
                    $new_name .= '.gif'; // 新ファイル名の末尾に拡張子「.gif」をつける
                    // サムネイルを保存
                    $image = imagecreatefromgif($old_name);
                    imagecopyresampled($canvas, $image, 0,0,0,0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
                    imagegif($canvas, __DIR__.'/../album/thumbs-'.$new_name);
                    break;

                case IMAGETYPE_PNG:
                    $new_name .= '.png'; // 新ファイル名の末尾に拡張子「.gif」をつける
                    // サムネイルを保存
                    $image = imagecreatefrompng($old_name);
                    imagecopyresampled($canvas, $image, 0,0,0,0, self::THUMBS_WIDTH, $thumbs_height, $width, $height);
                    imagepng($canvas, __DIR__.'/../album/thumbs-'.$new_name);
                    break;


                default:
                    // JPEG・GIF・PNG以外の画像なら処理しない
                    imagedestroy($canvas);
                    return null;
            }
            imagedestroy($canvas);
            imagedestroy($image);

            // 元サイズの画像をアップロード
            /**
             * move_uploaded_file
             * アップロードされて作成された仮ファイルを指定したパスのディレクトリに移動
             *
             * 第1パラメータ ・・・・・仮ファイルのパス
             * 第2パラメータ ・・・・・保存先のパス + 拡張子
             * */
            move_uploaded_file($old_name, __DIR__.'/../album/'.$new_name);

            // 保存したファイル名を返す
            return $new_name;


        } else {
            // 画像以外なら処理しない
            return null;
        }
    }

    public function save(){
        // bindParam用
        $title = $this->article->getTitle();
        // $body = $this->article->getBody();
        // $filename = null;

        if($this->article->getId()){
            // IDがあるときは上書き
            $id = $this->article->getId();
            $stmt = $this->dbh->prepare("UPDATE articles SET title = :title, body = :body, updated_at = NOW() WHERE id=:id");
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':body', $body, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // IDがなければ新規作成

            //呼び出し側、save()内のコード
            if ($file = $this->article->getFile()){
                $this->article->setFilename($this->saveFile($file['tmp_name']));
                $filename = $this->article->getFilename();
            }


            $stmt = $this->dbh->prepare("INSERT INTO articles (title, body, filename, created_at, updated_at)
                        VALUES (:title, :body, :filename, NOW(), NOW())");
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':body', $body, PDO::PARAM_STR);
            $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
            $stmt->execute();
        }
    }


    public function find($id){
        $stmt = $this->dbh->prepare("SELECT * FROM articles WHERE id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $article = null;
        if ($result){
            $article = new Article();
            $article->setId($result['id']);
            $article->setTitle($result['title']);
            $article->setBody($result['body']);
            $article->setFilename($result['filename']);
            $article->setCreatedAt($result['created_at']);
            $article->setUpdatedAt($result['updated_at']);
        }
        return $article;
    }



    public function findAll(){
        $stmt = $this->dbh->prepare("SELECT * FROM articles");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $articles = array();
        foreach ($results as $result){
            $article = new Article();
            $article->setId($result['id']);
            $article->setTitle($result['title']);
            $article->setBody($result['body']);
            $article->setFilename($result['filename']);
            $article->setCreatedAt($result['created_at']);
            $article->setUpdatedAt($result['updated_at']);
            $articles[] = $article;
        }
        return $articles;
    }
}

?>