<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $game['Game']['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

<style type="text/css">

	td {
		width: 156px;
		height: 96px;
		overflow: hidden;
		border: 1px;
		border-color: black;
	}
	td.correct {
		border-color: green;
	}
	
</style>
</head>
<body>
<h2><?php echo $game['Game']['title']; ?></h2>
<?php foreach($game['Question'] as $question): ?>
<h3><?php echo $question['title']; ?></h3>
Clue:<br />
<?php if($question['clue_type'] == 'image')
{
	if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question['id'].'.png')) echo '<img id="ClueImage" src="/img/clues/'.$question['id'].'.png?t='.time().'" />';
}else{
	echo $question['clue_text'];
} ?><br />
Question:<br />
<?php if($question['question_type'] == 'image')
{
	if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question['id'].'.png')) echo '<img id="QuestionImage" src="/img/questions/'.$question['id'].'.png?t='.time().'" />';
}else{
	echo $question['question_text'];
} ?><br />
Insight:<br />
<?php if($question['insight_type'] == 'image')
{
	if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question['id'].'.png')) echo '<img id="InsightImage" src="/img/insights/'.$question['id'].'.png?t='.time().'" />';
}else{
	echo $question['insight_text'];
} ?><br />
Answers:
<?php if($question['answer_type'] == 'image') { ?>
<table>
	<tr>
		<td<?php if($question['correct_answer'] == 0)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-1.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-1.png?t='.time().'" />'; ?>
		</td>
		<td<?php if($question['correct_answer'] == 1)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-2.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-2.png?t='.time().'" />'; ?>
		</td>
	</tr>
	<tr>
		<td<?php if($question['correct_answer'] == 2)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-3.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-3.png?t='.time().'" />'; ?>
		</td>
		<td<?php if($question['correct_answer'] == 3)  echo ' class="correct"'; ?>>
			<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question['id'].'-4.png')) echo '<img id="Answer1Image" src="/img/answers/'.$question['id'].'-4.png?t='.time().'" />'; ?>
		</td>
	</tr>
</table>
<?php }else{ ?>
<table>
	<tr>
		<td<?php if($question['correct_answer'] == 0)  echo ' class="correct"'; ?>>
			<?php echo $question['answer_1_text']; ?>
		</td>
		<td<?php if($question['correct_answer'] == 1)  echo ' class="correct"'; ?>>
			<?php echo $question['answer_2_text']; ?>
		</td>
	</tr>
	<tr>
		<td<?php if($question['correct_answer'] == 2)  echo ' class="correct"'; ?>>
			<?php echo $question['answer_3_text']; ?>
		</td>
		<td<?php if($question['correct_answer'] == 3)  echo ' class="correct"'; ?>>
			<?php echo $question['answer_4_text']; ?>
		</td>
	</tr>
</table>
<?php } ?>
<?php endforeach; ?>
</body>
</html>