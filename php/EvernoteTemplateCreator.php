<?php
class EvernoteTemplateCreator
{
	const TYPE_MONTHLY = 0;
	const TYPE_WEEKLY = 1;
	const TYPE_DAILY = 2;

	private static $typeAllArray = array(self::TYPE_MONTHLY, self::TYPE_WEEKLY, self::TYPE_DAILY);

	private static $tagArray = array(
		self::TYPE_MONTHLY => "monthly",
		self::TYPE_WEEKLY => "weekly" ,
		self::TYPE_DAILY => "daily",
	);

	private static $weekDayStringArray = array(
		0 => "日曜日",
		1 => "月曜日",
		2 => "火曜日",
		3 => "水曜日",
		4 => "木曜日",
		5 => "金曜日",
		6 => "土曜日",
	);

	public function __construct()
	{

	}

	public function createAllTemplate()
	{
		return $this->createTemplate(self::$typeAllArray);
	}

	private function createTemplate(array $typeArray)
	{
		$application = "Evernote/Windows";
		$evernoteVersion = "4.x";
		$dateTime = date("Ymd\THis\Z", $_SERVER["REQUEST_TIME"]);
		$year = date("Y", $_SERVER["REQUEST_TIME"]);
		$month = date("m", $_SERVER["REQUEST_TIME"]);
		$week = date("W", $_SERVER["REQUEST_TIME"]);
		$weekDay = date("w",$_SERVER["REQUEST_TIME"]);

		$outputDir = __DIR__ . '/../output/' . $year;
		if ( !file_exists($outputDir)){
			mkdir($outputDir);
		}

		foreach ($typeArray as $templateType){
			$noteTemplate = $this->getNoteTemplate($templateType, $year);
			$tag = self::$tagArray[$templateType];

			$template = file_get_contents(__DIR__ . "/../template/layout.xml");
			$template = preg_replace("/\[%CONTENTS%\]/", $noteTemplate, $template);
			$template = preg_replace("/\[%DATE_TIME%\]/", $dateTime, $template);
			$template = preg_replace("/\[%APPLICATION%\]/", $application, $template);
			$template = preg_replace("/\[%EVERNOTE_VERSION%\]/", $evernoteVersion, $template);
			$template = preg_replace("/\[%TAG%\]/", $tag, $template);

			$fp = fopen("{$outputDir}/{$tag}.enex", 'w');
			fwrite($fp, $template);
			fclose($fp);
		}

	}

	private function getNoteTemplate($templateType, $year)
	{
		$tag = self::$tagArray[$templateType];
		$noteTemplate = file_get_contents(__DIR__ . "/../template/{$tag}_note.xml");

		$content = "";
		switch ($templateType){
			case self::TYPE_MONTHLY :
				for ($i=1;$i<13;$i++){
					$title = "{$year}年{$i}月";
					$createDate = date("Ymd\THis\Z", mktime(1,0,0,$i,1,$year));

					$content .= $this->replaceNoteTemplate($noteTemplate, array("TITLE" => $title, "CREATE_DATE" => $createDate));

				}

				break;

			case self::TYPE_WEEKLY :
				$firstDay = null;
				for($i=1;$i<8;$i++){
					$firstDay = mktime(1,0,0,1,$i,$year);
					$weekDay = date("W//w",$firstDay);
					if($weekDay==="01//1")break;
				}

				for ($w=1;$w<54;$w++){
					$firstDayOfTheWeek = mktime(1,0,0,1,($w-1)*7+intval(date("d",$firstDay)));
					$lastDayOfTheWeek = mktime(1,0,0,1,($w-1)*7+intval(date("d",$firstDay))+6);
					$title = "{$year}年 第{$w}週(" . date('m/d',$firstDayOfTheWeek) . "月-" . date('m/d',$lastDayOfTheWeek) . "日)";
					$createDate = date("Ymd\THis\Z", $firstDayOfTheWeek);

					$content .= $this->replaceNoteTemplate($noteTemplate, array("TITLE" => $title, "CREATE_DATE" => $createDate));
				}

				break;

			case self::TYPE_DAILY :
				$today = date("z",$_SERVER["REQUEST_TIME"]);
				for($d=$today+1;$d<366;$d++){
					$theDay = mktime(1,0,0,1,$d,$year);
					$weekDay = self::$weekDayStringArray[intval(date("w",$theDay))];
					$title = "{$year}年". date('m',$theDay) . "月". date('d',$theDay) . "日" . $weekDay;
					$createDate = date("Ymd\THis\Z", $theDay);

					$content .= $this->replaceNoteTemplate($noteTemplate, array("TITLE" => $title, "CREATE_DATE" => $createDate));
				}

				break;
			default :
				throw new Exception("invalid template type: {$templateType}");
		}

		return $content;
	}

	private function replaceNoteTemplate($noteTemplate, array $replaceArray)
	{
		foreach ($replaceArray as $key => $value){
			$noteTemplate = preg_replace("/\[%{$key}%\]/", $value, $noteTemplate);
		}

		return $noteTemplate;
	}
}