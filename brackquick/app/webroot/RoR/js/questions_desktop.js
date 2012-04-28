var questions = [
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
	
	{
		title: 'Unplayed Question 2',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'image',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'New additions draw more power.',
		question: 'What are some reasons electricity bills might suddenly rise? ',
		answers: [
			'bg_answer_image_blank.png',
			'bg_answer_image_blank.png',
			'bg_answer_image_blank.png',
			'bg_answer_image_blank.png'
			],
		correct_answer_index: 3,
		insight: 'Just as new purchases can increase energy use, a new roommate or a new baby can raise your bill. (Be sure to plug new electronics into power strips and turn strips off when not in use.)'
	},
	
	{
		title: 'Unplayed Question 3',
		time: '12/03/12 10:16am',
		question_type: 'image',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'What you actually pay for energy used.',
		question: 'bg_answer_image_blank.png',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'A kilowatt is 1000 watts, and a kilowatt hour (kWh) is 1,000 watts of electricity used for one hour. To get a feel for how that translates to your wallet, check the rate per kWh noted on your bill.'
	},
	
	{
		title: 'Unplayed Question 4',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'R is for renewable--and rebates!',
		question: 'What is the "Renewable Energy Program" charge on my bill?',
		answers: [
			'A fee that supports alternative energy projects.',
			'A fee that helps fund rebate programs.',
			'&nbsp;&nbsp;A fee approved by the Public Utilities Commission of Nevada.',
			'All of the above.'
			],
		correct_answer_index: 3,
		insight: 'To find out what rebates might be available to you, go to nvenergy.com and enter "rebates" in the search box in the upper right corner.'
	},
	
	{
		title: 'Unplayed Question 5',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'Take care when you compare.!',
		question: 'Comparing your usage in total kilowatt hours each billing period is the best way to gauge conservation efforts.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 3,
		insight: 'Some billing periods have more days than others, so use the column that shows average kilowatt hours (kWh) per day to compare.'
	},
	
	{
		title: 'Unplayed Question 6',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'Calculating savings made easy.',
		question: 'The see how much reducing usage could lower your bill:',
		answers: [
			'Crunch the numbers yourself.',
			'Call the utility company.',
			'Compare bills with your neighbor.',
			'Use NV Energy’s online bill calculator.'
			],
		correct_answer_index: 3,
		insight: 'All you have to enter is the number of kilowatt hours. Select the correct calculator for Northern Nevada or Southern Nevada at nvenergy.com.<br />Source: nvenergy.com'
	},
	
	{
		title: 'Unplayed Question 7',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'Get smart.',
		question: 'What’s a smart meter?',
		answers: [
			'A digital meter that records energy usage.',
			'A device that transmits and receives data.',
			'Technology that can reduce operating costs and help lower customer bills.',
			'All of the above.'
			],
		correct_answer_index: 3,
		insight: 'Having a smart meter and monitoring the data online gives you information that can help you understand how to save.'
	},
	
	{
		title: 'Unplayed Question 8',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'image',
		category: 'How to Read a Bill',
		clue: 'Compare meter readings',
		question: 'The simplest way to see if your usage is up or down month over month is to compare the meter readings listed on your bill.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'bg_answer_image_blank.png'
	},
	
	{
		title: 'Unplayed Question 9',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'Smart meter, smarter customer. ',
		question: 'If you sign up for email alerts, NV Energy will notify you when your energy use or dollar amount for the billing period goes over a threshold you choose. Insight: If you have a smart meter installed and sign up to monitor your account online at nvenergy.com, you can check your energy use and projected bill—and even see how your consumption varies by day of the week and time of day.<br />Source: nvenergy.com',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'NOT PRESENT'
	},
	
	{
		title: 'Unplayed Question 10',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read a Bill',
		clue: 'Big Brother is not watching.',
		question: 'Smart meters aren’t surveillance devices.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'Smart meters record energy usage, just as older meters do. The information is sent over secure encrypted networks and NV Energy never shares customer data unless required to do so by law.'
	},
];