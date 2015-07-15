<?php
/**
 * @author LvChengbin
 */

Class PostModel extends BaseModel {
    protected $table = 'posts';

    public function getPostsByAccount( $account, $order, $start = 0, $rn = 20, $columns = null ) {
        $query = sprintf( 'SELECT ' . $this->formatColumns( $columns ) . ' FROM `posts` WHERE `account_id` = :account_id ORDER BY %s LIMIT :start, :rn', $order);

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':account_id', $account );
            $stmt->bindValue( ':start', (int)$start, PDO::PARAM_INT );
            $stmt->bindValue( ':rn', (int)$rn, PDO::PARAM_INT );

            $stmt->execute();

            $posts = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $this->db->commit();

            return $posts;
        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
