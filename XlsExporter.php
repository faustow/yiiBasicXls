<?php

class XlsExporter
{
    const CRLF = "\r\n";

    /**
     * Export and download an Active Record resultset to an XML-based xls file
     *
     * @param $filename - Name of output filename
     * @param $data - Active record data set
     * @param $title - Title displayed on top
     * @param $header - Boolean to show/hide header
     * @param $labels - Array of fields to export
     * @param $type - String that explains what's being exported for the end user (use plural)
     */
    public static function downloadXls($filename, $data, $title = false, $header = false, $labels = false, $fields = false,$type = 'lines')
    {
        $export = self::createXlsString($data, $title, $header, $labels, $fields, $type);
        self::sendHeader($filename, strlen($export), 'vnd.ms-excel');
        echo $export;
        Yii::app()->end();
    }

    /**
     * Send file header
     *
     * @param $filename - Filename for the created file
     * @param $length - Size of file
     * @param $type - Mime type of exported data
     */
    private static function sendHeader($filename, $length, $type = 'octet-stream')
    {
        if (strtolower(substr($filename, -4)) != '.xls'){
            $filename .= '.xls';
        }

        header("Content-type: application/$type");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-length: $length");
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     * Private method to create xls string from active record data set
     *
     * @param $data - CActiveRecord or Array
     * Example: 1.CActiveRecord: $data = Model::model()->findAll(new CDbCriteria()->condition = "column > $param");
     *          2.Array: $data = Yii::app()->db->createCommand("SELECT * FROM t")->queryAll()
     *
     * @param $title - Title displayed on top
     * @param $header - Boolean to show/hide header
     * @param $labels - Array of fields to show in header
     *
     * @param $fields -Array of fields to export: alias, relation's names or array's index
     *  example: 1.CActiveRecord: "firstRel.secondRel.n.propertyOfLastRel"
     *           2.Array: "index1" (alias or column name) example: SELECT col as index1 FROM t;
     *
     * @param $type - String that explains what's being exported for the end user
     */
    private static function createXlsString($data, $title, $header, $labels, $fields, $type)
    {
        $string = '<html>' . self::CRLF
            . '<head>' . self::CRLF
            . '<meta http-equiv="content-type" content="text/html; charset=utf-8">' . self::CRLF
            //The following line of code is to allow long numbers (such as bank account number )
            . '<style> .text{ mso-number-format:\@; } </style>' . self::CRLF
            . '</head>' . self::CRLF
            . '<body style="text-align:center">' . self::CRLF;

        if ($title)
            $string .= "<b>$title</b><br /><br />" . self::CRLF
                . Yii::t('main', 'Exported '.$type) . ': ' . count($data) . '<br />' . self::CRLF
                . Yii::t('main', 'Export date') . ': ' . Yii::app()->dateFormatter->formatDateTime($_SERVER['REQUEST_TIME']) . '<br /><br />' . self::CRLF;

        if ($data)
        {
            $string .= '<table style="text-align:left" border="1" cellpadding="1" cellspacing="1">' . self::CRLF;

            if (!$labels)
                $labels = array_keys($data[0]->attributes);

            if ($header)
            {
                $string .= '<tr>' . self::CRLF;
                foreach ($labels as $field)
                    $string .= '<th>' . $field . '</th>' . self::CRLF;
                $string .= '</tr>' . self::CRLF;
            }
            foreach ($data as $row)
            {
                $isDataProvider=strpos(print_r($data[0], true),"CActiveRecord");
                $string .= '<tr>' . self::CRLF;
                foreach ($fields as $dato){
                    if($isDataProvider){
                        //$data is a CActiveDataProvider AND relational query
                        $pos = strpos($dato, ".");
                        $dato_r = $row;
                        while ($pos !== false) {
                            $field = substr($dato, 0, $pos - strlen($dato));
                            $dato_r = $dato_r->$field;
                            $dato = substr($dato, $pos + 1, strlen($dato));
                            $pos = strpos($dato, ".");
                        }
                        if($dato_r->$dato){
                            $final = $dato_r->$dato;
                        }else{
                            //The field value is null
                            $final="";
                        }
                    }else{
                        //$data is an array data, result of: Yii::app()->db->createCommand("SELECT * FROM tabla")->queryAll()
                        $final = $row[$dato];
                    }
                    $string .= '<td class="text">' . $final . '</td>' . self::CRLF;
                }
                $string .= '</tr>' . self::CRLF;
            }
            $string .= '</table>' . self::CRLF;
        }

        $string .= '</body>' . self::CRLF
            . '</html>';

        return $string;
    }
}