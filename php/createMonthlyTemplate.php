<?php
/**
 * Evernoteのテンプレートを作成するクラス。evernoteの最小単位は以下
 * <note><title>無題ノート</title><content><![CDATA[<?xml version="1.0" encoding="UTF-8"?>
 * <!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">;
 * <en-note style="word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space; ">あいうえお</en-note>]]></content><created>20100601T032014Z</created><updated>20100601T032023Z</updated><tag>daily</tag><note-attributes/></note>
 *
 * サンプル
 *
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE en-export SYSTEM "http://xml.evernote.com/pub/evernote-export.dtd">
	<en-export export-date="20100927T233947Z" application="Evernote" version="Evernote Mac 1.10.1 (93489)">
		<note>
			<title>テスト1984年8月第2週</title>
			<content>
				<![CDATA[<?xml version="1.0" encoding="UTF-8"?>
				<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">
				<en-note style="word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space; ">
					*************************目標****************************<div>*************************作業内容**********************</div><div>*************************レビュー**********************</div>
				</en-note>]]>
			</content>
			<created>20100927T232819Z</created>
			<updated>20100927T233921Z</updated>
			<tag>weekly</tag>
			<note-attributes/>
		</note>
		<note>
			<title>テスト1984</title>
			<content>
				<![CDATA[<?xml version="1.0" encoding="UTF-8"?>
				<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">
				<en-note style="word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space; ">
					******************目標**************************<div><br /></div><div>******************レビュー********************</div>
				</en-note>]]>
			</content>
			<created>20100927T232621Z</created>
			<updated>20100927T233927Z</updated>
			<tag>year</tag>
			<note-attributes/>
		</note>
</en-export>

 */

$application = "Evernote/Windows";
$evernoteVersion = "4.x";
//TODO 文字列結合が必要
$dateTime = date("Ymd\THis\Z", $_SERVER["REQUEST_TIME"]);
$year = date("Y", $_SERVER["REQUEST_TIME"]);
$month = date("m", $_SERVER["REQUEST_TIME"]);
$outputDir = __DIR__ . '/../output/' . $year;
mkdir($outputDir);
//echo $dateTime;
//echo "year=" . $year;
$outputString = "";

//TODO 10年毎の出力配列を作成
$tenYearthString = "";

//TODO 年毎の出力配列を作成


//TODO 月ごとの出力配列を作成
$tag = "monthly";

$content = "";
for($i=1;$i<13;$i++){
	$title = "{$year}年{$i}月";
	$createDate = date("Ymd\THis\Z", mktime(1,0,0,$i,1,$year));

	$noteTemplate = file_get_contents("../template/monthly_note.xml");
	$noteTemplate = preg_replace("/\[%TITLE%\]/", $title, $noteTemplate);
	$noteTemplate = preg_replace("/\[%CREATE_DATE%\]/", $createDate, $noteTemplate);
	$content .= $noteTemplate;
}
$monthlyTemplate = file_get_contents("../template/monthly.xml");
$monthlyTemplate = preg_replace("/\[%CONTENTS%\]/", $content, $monthlyTemplate);
$monthlyTemplate = preg_replace("/\[%DATE_TIME%\]/", $dateTime, $monthlyTemplate);
$monthlyTemplate = preg_replace("/\[%APPLICATION%\]/", $application, $monthlyTemplate);
$monthlyTemplate = preg_replace("/\[%EVERNOTE_VERSION%\]/", $evernoteVersion, $monthlyTemplate);
$monthlyTemplate = preg_replace("/\[%TAG%\]/", $tag, $monthlyTemplate);


$fp = fopen($outputDir.'/monthly.enex', 'w');
fwrite($fp, $monthlyTemplate);
fclose($fp);

//TODO 週ごとの出力配列を作成
$month = date("m", $_SERVER["REQUEST_TIME"]);
//年の最初の週の最初の日を求める
for($i=1;$i<8;$i++){
	$firstDay = mktime(1,0,0,1,$i,$year);
	$weekDay = date("W//w",$firstDay);
	echo "weekDay::".$weekDay."<br>";
	if($weekDay==="01//1")break;
}
echo "weekDay::".$weekDay."<br>";
$week = date("W", $_SERVER["REQUEST_TIME"]);
$weekDay = date("w",$_SERVER["REQUEST_TIME"]);
//TODO 今日の週の値wに7をかけてfirstDayに足す
$firstDayOfTheWeek = mktime(1,0,0,1,($week-1)*7+intval(date("d",$firstDay)));
$lastDayOfTheWeek = mktime(1,0,0,1,($week-1)*7+intval(date("d",$firstDay))+6);

echo "dateTest :: ". date("d",$firstDay)."<br />";
echo "firstDayOfTheWeek :: ". date("Ymd",$firstDayOfTheWeek)."<br />";
echo "lastDayOfTheWeek :: ". date("Ymd",$lastDayOfTheWeek)."<br />";

echo "weekly\n<br>";
$tag = "weekly";
$weeklyString = "";
$weeklyString .= '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE en-export SYSTEM "http://xml.evernote.com/pub/evernote-export.dtd">';
$weeklyString .= '<en-export export-date="'.$dateTime.'" application="Evernote" version="'.$evernoteVersion.'}">';
for($w=1;$w<54;$w++){
	$firstDayOfTheWeek = mktime(1,0,0,1,($w-1)*7+intval(date("d",$firstDay)));
	$lastDayOfTheWeek = mktime(1,0,0,1,($w-1)*7+intval(date("d",$firstDay))+6);
	$title = "{$year}年 第{$w}週(" . date('m/d',$firstDayOfTheWeek) . "月-" . date('m/d',$lastDayOfTheWeek) . "日)";
	$weeklyString .= '<note><title>'.$title.'</title><content><![CDATA[<?xml version="1.0" encoding="UTF-8"?>';
	$weeklyString .= '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">';
	$weeklyString .= '<en-note style="word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space; ">'.$title;
	$weeklyString .= '<div>*************************目標****************************</div>';
	$weeklyString .= '<div>*************************スケジュール****************************</div>';
	$weeklyString .= '<div>*************************TODO**********************</div>';
	$weeklyString .= '<div>*************************レビュー**********************</div>';
	$weeklyString .= '</en-note>]]></content><created>'.date("Ymd\THis\Z", $firstDayOfTheWeek).'</created><updated>'.$dateTime.'</updated><tag>'.$tag.'</tag><note-attributes/></note>';
}
$weeklyString .= '</en-export>';
echo $weeklyString;
$fp = fopen($outputDir.'/weekly.enex', 'w');
fwrite($fp, $weeklyString);
fclose($fp);

//TODO 日毎の出力配列を作成-->
echo "\daily\n<br>";
$tag = "daily";
$firstDayOfTheWeek = mktime(1,0,0,1,($week-1)*7+intval(date("d",$firstDay)));
$lastDayOfTheWeek = mktime(1,0,0,1,($week-1)*7+intval(date("d",$firstDay))+6);


$dailyString = "";
$dailyString .= '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE en-export SYSTEM "http://xml.evernote.com/pub/evernote-export.dtd">';
$dailyString .= '<en-export export-date="'.$dateTime.'" application="Evernote" version="'.$evernoteVersion.'}">';
$today = date("z",$_SERVER["REQUEST_TIME"]);
for($d=$today+1;$d<366;$d++){
	//TODO 曜日
	$theDay = mktime(1,0,0,1,$d,$year);
	switch (intval(date("w",$theDay))) {
		case 0:
			$weekDay = "日曜日";
			break;
		case 1:
			$weekDay = "月曜日";
			break;
		case 2:
			$weekDay = "火曜日";
			break;
		case 3:
			$weekDay = "水曜日";
			break;
		case 4:
			$weekDay = "木曜日";
			break;
		case 5:
			$weekDay = "金曜日";
			break;
		case 6:
			$weekDay = "土曜日";
			break;
		default:
			$weekDay = "error";
	}
	$title = "{$year}年". date('m',$theDay) . "月". date('d',$theDay) . "日" . $weekDay;
	$dailyString .= '<note><title>'.$title.'</title><content><![CDATA[<?xml version="1.0" encoding="UTF-8"?>';
	$dailyString .= '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">';
	$dailyString .= '<en-note style="word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space; ">'.$title;
	$dailyString .= '<div>*************************目標****************************</div>';
	$dailyString .= '<div>*************************スケジュール****************************</div>';
	$dailyString .= '<div>*************************TODO**********************</div>';
	$dailyString .= '<div>*************************メモ**********************</div>';
	$dailyString .= '<div>*************************レビュー**********************</div>';
	$dailyString .= '</en-note>]]></content><created>'.date("Ymd\THis\Z", $theDay).'</created><updated>'.$dateTime.'</updated><tag>'.$tag.'</tag><note-attributes/></note>';
}
$dailyString .= '</en-export>';
echo $dailyString;
$fp = fopen($outputDir.'/daily.enex', 'w');
fwrite($fp, $dailyString);
fclose($fp);