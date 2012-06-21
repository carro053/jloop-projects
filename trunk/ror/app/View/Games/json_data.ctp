<?php
echo '
var questions = [';
$i=0;
foreach($game['Question'] as $key=>$question):
	if(isset($question['QuestionVersion'][0]['id']) && ($question['QuestionVersion'][0]['deleted'] == 0 || count($game['Question']) == 1))
	{
	if($i != 0) echo ',
	';
	echo '
	{
		title: \'Unplayed Question '.($i+1).'\',
		time: \''.$question['QuestionVersion'][0]['time'].'\',
		question_type: \''.$question['QuestionVersion'][0]['question_type'].'\',
		answer_type: \''.$question['QuestionVersion'][0]['answer_type'].'\',
		clue_type: \''.$question['QuestionVersion'][0]['clue_type'].'\',
		insight_type: \''.$question['QuestionVersion'][0]['insight_type'].'\',
		category: \''.$game['Game']['title'].'\',';
		echo 'learn_more_url: \''.$question['QuestionVersion'][0]['learn_more_url'].'\',';
		if($question['QuestionVersion'][0]['clue_type'] == 'image')
		{
			echo '
		clue: \''.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png\',';
		}else{
			echo '
		clue: \''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['clue_text'], ENT_QUOTES)).'\',';
		}
		if($question['QuestionVersion'][0]['question_type'] == 'image')
		{
			echo '
		question: \''.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png\',';
		}else{
			echo '
		question: \''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['question_text'], ENT_QUOTES)).'\',';
		}
		if($question['QuestionVersion'][0]['answer_type'] == 'image')
		{
			echo '
		answers: [
			\''.$question['id'].'-1-'.$question['QuestionVersion'][0]['id'].'.png\',
			\''.$question['id'].'-2-'.$question['QuestionVersion'][0]['id'].'.png\',
			\''.$question['id'].'-3-'.$question['QuestionVersion'][0]['id'].'.png\',
			\''.$question['id'].'-4-'.$question['QuestionVersion'][0]['id'].'.png\'
			],';
		}else{
			echo '
		answers: [';
			if($question['QuestionVersion'][0]['answer_1_text'] == '')
			{
				echo '
			null,';
			}else{
				echo '
			\''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['answer_1_text'], ENT_QUOTES)).'\',';
			}
			if($question['QuestionVersion'][0]['answer_2_text'] == '')
			{
				echo '
			null,';
			}else{
				echo '
			\''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['answer_2_text'], ENT_QUOTES)).'\',';
			}
			if($question['QuestionVersion'][0]['answer_3_text'] == '')
			{
				echo '
			null,';
			}else{
				echo '
			\''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['answer_3_text'], ENT_QUOTES)).'\',';
			}
			if($question['QuestionVersion'][0]['answer_4_text'] == '')
			{
				echo '
			null,';
			}else{
				echo '
			\''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['answer_4_text'], ENT_QUOTES)).'\'';
			}
			echo '
			],';
		}
		echo '
		correct_answer_index: '.$question['QuestionVersion'][0]['correct_answer'].',';
		if($question['QuestionVersion'][0]['insight_type'] == 'image')
		{
			echo '
		insight: \''.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png\'';
		}else{
			echo '
		insight: \''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['insight_text'], ENT_QUOTES)).'\'';
		}
		if($question['QuestionVersion'][0]['has_prize'])
		{
			echo ',
		prize: \''.$question['id'].'-'.$question['QuestionVersion'][0]['id'].'.png\',
		prize_text: \''.str_replace("'","\\'",htmlspecialchars_decode($question['QuestionVersion'][0]['prize_text'], ENT_QUOTES)).'\'';
		}
		echo '
	}';
	$i++;
	}
endforeach;
echo '
];';
	/*{
		title: 'Unplayed Question 2',
		time: 'Two days ago, 1:22pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'image',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'desktop_question_3.png',
		question: 'Electricity consumed during the billing period determines the amount you pay.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'You control the amount you pay for consumption. The less you use, the less you pay.'
	},
	{
		title: 'Unplayed Question 3',
		time: 'Two days ago, 3:12pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'Level payment plans help even out monthly costs.',
		question: 'Seasonal weather can change your month over month electricity cost.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'NV Energy\'s Equal Payment Plan lets you even out your costs over the year.'
	},
	
	
	
	{
		title: 'Unplayed Question 4',
		time: 'Yesterday, 10:15am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'image',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'desktop_clue_1.png',
		question: 'What part of your bill shows conservation efforts over time?',
		answers: [
			'Total amount due.',
			'Previous balance.',
			'The bar graph of your use.',
			'All of the above.'
			],
		correct_answer_index: 2,
		insight: 'Conserving energy takes effort but it\'s worth it. Do a little each month for best results.'
	},

	
	{
		title: 'Unplayed Question 5',
		time: 'Yesterday, 1:31pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'Calculating savings made easy.',
		question: 'To see how much reducing usage could lower your bill:',
		answers: [
			'Crunch the numbers yourself.',
			'Call the utility company.',
			'Compare bills with your neighbor.',
			'Use NV Energy\'s online bill calculator.'
			],
		correct_answer_index: 3,
		insight: 'Select the correct calculator for Northern Nevada or Southern Nevada at nvenergy.com.'
	},
	
	
	{
		title: 'Unplayed Question 6',
		time: 'Yesterday, 4:11pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'The search word is \'rebate\'.',
		question: 'How do I participate in rebate programs that are partly paid from my bill?',
		answers: [
			'Ask a retailer.',
			'Search "rebate" on nvenergy.com',
			'Write the Public Utilities Commission.',
			'None of the above.'
			],
		correct_answer_index: 1,
		insight: 'The \'Renewable Energy Program\' charge on your bill funds alternative energy and rebate programs.'
	},
	
	
	
	
	
	{
		title: 'Unplayed Question 7',
		time: 'Today, 9:44am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'image',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'clue_billing_2.png',
		question: 'The billing usage field tells you how much energy you have used in a month.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'The billing usage is also the number you need to use the online calculator at nvenergy.com'
	},
	//////NEVADA
	{
		title: 'Unplayed Question 8', 
		time: 'Today 11:42am',
		question_type: 'text',
		answer_type: 'image',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'BONUS QUESTION!<br/><br/>Two pines intertwined.',
		question: 'What is the Nevada state tree? ',
		answers: [
			'tree-01.png',
			'tree-02.png',
			'tree-03.png',
			'tree-04.png'
		],
		correct_answer_index: 3,
		insight: 'The Bristlecone Pine, which shares the state-tree designation, is considered the oldest living thing on earth, with some estimated at more than 4,000 years old.'
	},
	
	{
		title: 'Unplayed Question 9',
		time: 'Today, 12:20pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'image',
		category: 'How to Read Your Bill',
		clue: 'The \'meter readings\' section on your bill helps you manage monthly usage and cost',
		question: 'Comparing the meter readings listed on your bill is a simple way to see how you\'re doing month over month.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'desktop_insight_8.png'
	},
	

	{
		title: 'Unplayed Question 10',
		time: 'Today, 5:01pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'Some billing periods have more days than others',
		question: 'Comparing total amounts from this month\'s bill and last month\'s bill is <u>always</u> an accurate way to compare my energy usage.',
		
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 3,
		insight: 'Take care when you compare. Use the column that shows average kilowatt hours (kWh) per day to compare.',
	},

];*/ ?>