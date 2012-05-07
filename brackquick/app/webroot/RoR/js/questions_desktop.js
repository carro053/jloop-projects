var questions = [
/*
	{
		title: 'Unplayed Question 1',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'Understand your bill',
		question: 'The best place to learn how to read your bill is:',
		answers: [
			'Google search',
			'nvenergy.com',
			'YouTube',
			'Ask a neighbor'
			],
		correct_answer_index: 1,
		insight: 'Your bill has great information to save you money and improve energy efficiency. A picture of it, with each section explained, can be found the FAQ section of the NV energy website.'
	},
*/	
	{
		title: 'Unplayed Question 1',
		time: 'Two days ago, 9:52am',
		question_type: 'text',
		answer_type: 'image',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'New additions draw more power.',
		question: 'What are some reasons electricity bills might suddenly rise? ',
		answers: [
			'desktop_answer_2_1.png',
			'desktop_answer_2_2.png',
			'desktop_answer_2_3.png',
			'all_of_the_above.png'
			],
		correct_answer_index: 3,
		insight: 'One way to lower the cost of new appliances is to turn them off when not in use.',
		prize: 'giftcard-36.png'
	},
	
	{
		title: 'Unplayed Question 2',
		time: 'Two days ago, 1:22pm',
		question_type: 'image',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'What you actually pay for energy used.',
		question: 'desktop_question_3.png',
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
		category: 'How to Read a Bill',
		clue: 'Level payment plans help even out monthly costs.',
		question: 'Most of us have higher bills in summer and winter.',
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
		category: 'How to Read a Bill',
		clue: 'desktop_clue_1.png',
		question: 'What part of your bill shows conservation efforts over time?',
		answers: [
			'The total column.',
			'Comparing meter months.',
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
		category: 'How to Read a Bill',
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
		category: 'How to Read a Bill',
		clue: 'The search word is \'rebate\'.',
		question: 'How do I participate in rebate programs that are partly paid from my bill?',
		answers: [
			'Ask a retailer.',
			'Insert "Rebate" in the search box on NV Energy.com',
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
		category: 'How to Read a Bill',
		clue: 'image',
		question: 'The billing usage tells you how much energy you used in a month.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'The billing usage is the number you need to use the on line calculator at NV Energy.com'
	},
	//////NEVADA
	{
		title: 'Unplayed Question 8', 
		time: 'Today 11:42am',
		question_type: 'text',
		answer_type: 'image',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'Two pines intertwined.',
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
		category: 'How to Read a Bill',
		clue: 'The \'meter readings\' section on your bill helps you manage monthly usage and cost',
		question: 'Comparing the meter readings listed on your bill is the simplest way to see how you\'re doing month over month.',
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
		category: 'How to Read a Bill',
		clue: 'Some billing periods have more days than others',
		question: 'I can accurately compare my month over month usage.',
		
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 3,
		insight: 'Take care when you compare. Use the column that shows average kilowatt hours (kWh) per day to compare.',
	},

];