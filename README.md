# yiiBasicXls
Basic Yii Component to export and download an Active Record resultset as an XML-based xls file
Tested on Yii version 1.0.4.

## Installation
Download **XlsExporter.php** and move it into **/protected/components/** in your own Yii application.

## Usage
Once the XlsExporter component is in place, you just need to:
+ Search for whatever data you want to create the Xls file about
+ Call the downloadXls function with the correct parameters:
  + filename: String that will be used as name for the output filename. No need to append '.xls' since it will be added by default. (**required**)
  + data: Active record data set (**required**)
  + title: String that will be used as the title displayed on top of the exported lines. *Defaults to false.*
  + header: Boolean to show/hide header. *Defaults to false.*
  + fields: Array of fields to export. *Defaults to false.*
  + type: String that explains what's being exported for the end user (use plural) like 'users', 'cars', etc. *Defaults to 'lines'.*

For example (on some controller):
```
public function actionDownloadReport() {
	$fields = array('email', 'firstName', 'lastName', 'type');

	$criteria = new CDbCriteria();
	$criteria->select = $fields;
	$criteria->condition = "firstName = 'John'";
	$criteria->order = 'lastName';
	$users = users::model()->findAll($criteria);

	XlsExporter::downloadXls('report', $users, 'List of users called John', true, $fields, 'users');
}
```
Once that's in place, calling **yoursite.com/somecontroller/downloadReport** will trigger the download of the xls file in the browser.

## Credits
Based on the work of yii user amc.
