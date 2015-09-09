<?php

namespace Reports;

Class Api {
    static private $reportModel;

    static private function getReportModel() {
        if( self::$reportModel ) return self::$reportModel;
        self::$reportModel = new \ReportModel();
        return self::$reportModel;
    }

    static public function get( $id, $columns = null ) {
        return self::getReportModel()->get( $id, $columns );
    }

    static public function firstUntreatedReport() {
        $reports = self::getReportModel()->select( [
            'where' => [
                [ 'status', 0]
            ]
        ] );

        return count( $reports ) > 0 ? $reports[0] : false;
    }

    static public function recentReports( $account_id, $len ) {
        $count = self::getReportModel()->select( [
            'columns' => ''
        ] );
    }

}
