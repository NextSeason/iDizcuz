<?php

Class ReportModel extends BaseModel {
    protected $table = 'reports';

    public function getReportByPostAndAccount( $post, $account, $status = 0 ) {

        $query = 'SELECT * FROM `reports` WHERE `post_id`=:post AND `account_id`=:account AND `status`=:status';

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare( $query );
            $stmt->bindValue( ':post', $post );
            $stmt->bindValue( ':account', $account );
            $stmt->bindValue( ':status', $status );

            if( !$stmt->execute() ) {
                throw new PDOException( 'failed to get data from reports' );
            }

            $this->db->commit();

            return $stmt->fetch( PDO::FETCH_ASSOC );

        } catch( PDOException $e ) {
            $this->db->rollback();
            return false;
        }
    }
}
