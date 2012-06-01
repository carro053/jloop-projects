<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Game: <?php echo $game['Game']['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

<style type="text/css">
	body {
		font-family: Arial;
		width: 800px;
		text-align: center;
		
	}
	td {
		width: 156px;
		height: 96px;
		overflow: hidden;
		border: solid 1px;
		border-color: black;
		text-align: center;
	}
	td.correct {
		border: solid 3px;
		border-color: green;
	}
	div {
		width: 326px;
		margin:0 auto;
		text-align: center;
	}
	table { 
		margin:0 auto;
	}
	div.question {
		page-break-after:always;
		width: 800px;
		text-align: center;
		
	}
	div.last-question {
		width: 800px;
		text-align: center;
		
	}
</style>
</head>
<body>
<?php /*<h2>Game: <?php echo $game['Game']['title']; ?></h2>*/ ?>
<?php
$i = 0;
foreach($game['Question'] as $question):
if(isset($question['QuestionVersion'][0]['id']) && $question['QuestionVersion'][0]['deleted'] == 0) {
if(($i + 1) == count($game['Question'])) { ?>
<div class="last-question">
<?php }else{ ?>
<div class="question">
<?php } ?>
<h3>Question #<?php echo ($i + 1); ?>: <?php echo $question['QuestionVersion'][0]['title']; ?> (Version <?php echo $question['QuestionVersion'][0]['version']; ?>)</h3>
Clue:<br />
<div>
<?php if($question['QuestionVersion'][0]['clue_type'] == 'image')
{
	if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png')) echo '<img id="ClueImage" src="/img/clues/'.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png?t='.time().'" />';
}else{
	echo $question['QuestionVersion'][0]['clue_text'];
} ?>
</div>
<br />
Question:<br />
<div>
<?php if($question['QuestionVersion'][0]['question_type'] == 'image')
{
	if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png')) echo '<img id="QuestionImage" src="/img/questions/'.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png?t='.time().'" />';
}else{
	echo $question['QuestionVersion'][0]['question_text'];
} ?>
</div>
<br />
Answers:
<?php if($question['QuestionVersion'][0]['answer_type'] == 'image') { ?>
<table>
	<tr>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 0)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-1-'.$question['QuestionVersion'][0]['id'].'.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-1-'.$question['QuestionVersion'][0]['id'].'.png?t='.time().'" />'; ?>
		</td>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 1)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-2-'.$question['QuestionVersion'][0]['id'].'.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-2-'.$question['QuestionVersion'][0]['id'].'.png?t='.time().'" />'; ?>
		</td>
	</tr>
	<tr>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 2)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-3-'.$question['QuestionVersion'][0]['id'].'.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-3-'.$question['QuestionVersion'][0]['id'].'.png?t='.time().'" />'; ?>
		</td>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 3)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-4-'.$question['QuestionVersion'][0]['id'].'.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-4-'.$question['QuestionVersion'][0]['id'].'.png?t='.time().'" />'; ?>
		</td>
	</tr>
</table>
<?php }else{ ?>
<table>
	<tr>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 0)  echo ' class="correct"'; ?>>
			<?php echo $question['QuestionVersion'][0]['answer_1_text']; ?>
		</td>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 1)  echo ' class="correct"'; ?>>
			<?php echo $question['QuestionVersion'][0]['answer_2_text']; ?>
		</td>
	</tr>
	<tr>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 2)  echo ' class="correct"'; ?>>
			<?php echo $question['QuestionVersion'][0]['answer_3_text']; ?>
		</td>
		<td<?php if($question['QuestionVersion'][0]['correct_answer'] == 3)  echo ' class="correct"'; ?>>
			<?php echo $question['QuestionVersion'][0]['answer_4_text']; ?>
		</td>
	</tr>
</table>
<?php } ?>
<br />
Insight:<br />
<div>
<?php if($question['QuestionVersion'][0]['insight_type'] == 'image')
{
	if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png')) echo '<img id="InsightImage" src="/img/insights/'.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png?t='.time().'" />';
}else{
	echo $question['QuestionVersion'][0]['insight_text'];
} ?>
</div>
</div>
<?php 
	$i++;
}
endforeach; ?>
</body>
</html>