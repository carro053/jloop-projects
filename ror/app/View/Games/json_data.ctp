<?php
$html = '';
$html .= '[';
foreach($game['Question'] as $key=>$question):
if($key != 0) $html .= ',';
$html .= '{title:\'Unplayed Question '.($key+1).'\',time: \''.$question['time'].'\',question_type: \''.$question['question_type'].'\',answer_type:\''.$question['answer_type'].'\',clue_type:\''.$question['clue_type'].'\',insight_type:\''.$question['insight_type'].'\',category:\''.$game['Game']['title'].'\',';if($question['clue_type'] == 'image')
{
$html .= 'clue:\''.$question['id'].'.png\',';
}else{
$html .= 'clue: \''.nl2br($question['clue_text']).'\',';
}
if($question['question_type'] == 'image')
{
$html .= 'question: \''.$question['id'].'.png\',';}else{$html .= 'question: \''.nl2br($question['question_text']).'\',';}if($question['answer_type'] == 'image'){$html .= 'answers: [\''.$question['id'].'_1.png\',\''.$question['id'].'_2.png\',\''.$question['id'].'_3.png\',\''.$question['id'].'_4.png\'],';}else{$html .= 'answers: [';if($question['answer_1_text'] == ''){$html .= 'null,';}else{$html .= '\''.nl2br($question['answer_1_text']).'\',';}if($question['answer_2_text'] == ''){$html .= 'null,';}else{$html .= '\''.nl2br($question['answer_2_text']).'\',';}if($question['answer_3_text'] == ''){$html .= 'null,';}else{$html .= '\''.nl2br($question['answer_3_text']).'\',';}if($question['answer_4_text'] == ''){$html .= 'null,';}else{$html .= '\''.nl2br($question['answer_4_text']).'\'';}$html .= '],';}$html .= 'correct_answer_index: '.$question['correct_answer'].',';if($question['insight_type'] == 'image'){$html .= 'insight: \''.$question['id'].'.png\'';}else{$html .= 'insight: \''.nl2br($question['insight_text']).'\'';}$html .= '}';
endforeach;
$html .= '
];';
echo $html;
$new_html = json_decode($html);
echo 'var question = '.json_encode($new_html);
?>