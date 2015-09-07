<?php

Class ArticleModel extends BaseModel {
    protected $table = 'articles';

    /**
     * get all articles without any conditions
     * always use for the article list page in mis
     */
    public function getArticles( $params ) {
        $start = $params['start'];
        $rn = $params['rn'];

        $query = 'SELECT * FROM `articles` ORDER BY `id` DESC LIMIT :start, :rn';
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );

            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from articles' );
            }

            $articles = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();
            return $articles;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
