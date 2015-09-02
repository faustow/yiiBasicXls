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
     * @param $fields - Array of fields to export
     * @param $type - String that explains what's being exported for the end user (use plural)
     */
    public static function downloadXls($filename, $data, $title = false, $header = false, $fields = false, $type = 'lines')
    {
        $export = self::createXlsString($data, $title, $header, $fields, $type);
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
     * @param $data - Active record data set
     * @param $title - Title displayed on top
     * @param $header - Boolean to show/hide header
     * @param $fields - Array of fields to export
     * @param $type - String that explains what's being exported for the end user
     */
    private static function createXlsString($data, $title, $header, $fields, $type)
    {
        $string = '<html>' . self::CRLF
        . '<head>' . self::CRLF
        . '<meta http-equiv="content-type" content="text/html; charset=utf-8">' . self::CRLF
        . '</head>' . self::CRLF
        . '<body style="text-align:center">' . self::CRLF;

        if ($title)
            $string .= "<b>$title</b><br /><br />" . self::CRLF
            . Yii::t('main', 'Exported '.$type) . ': ' . count($data) . '<br />' . self::CRLF
            . Yii::t('main', 'Export date') . ': ' . Yii::app()->dateFormatter->formatDateTime($_SERVER['REQUEST_TIME']) . '<br /><br />' . self::CRLF;

        if ($data)
        {
            $string .= '<table style="text-align:left" border="1" cellpadding="0" cellspacing="0">' . self::CRLF;

            if (!$fields)
                $fields = array_keys($data[0]->attributes);

            if ($header)
            {
                $string .= '<tr>' . self::CRLF;
                foreach ($fields as $field)
                    $string .= '<th>' . $data[0]->getAttributeLabel($field) . '</th>' . self::CRLF;
                $string .= '</tr>' . self::CRLF;
            }

            foreach ($data as $row)
            {
                $string .= '<tr>' . self::CRLF;
                foreach ($fields as $field)
                    $string .= '<td>' . $row->$field . '</td>' . self::CRLF;
                $string .= '</tr>' . self::CRLF;
            }

            $string .= '</table>' . self::CRLF;
        }

        $string .= '</body>' . self::CRLF
        . '</html>';

        return $string;
    }
}