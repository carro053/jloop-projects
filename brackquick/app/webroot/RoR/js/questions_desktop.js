var questions = [
	{
		title: 'Unplayed Question 1',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'image',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_url: 'https://www.nvenergy.com/home/paymentbilling/myaccount.cfm',
		clue: 'Joe from Carson City is "upgrading" his stuff. (Translation: he\'s reliving his youth). Joe\'s wife warns him that his new stuff may be more expensive than the pricetags show...',
		question: 'Which of these new additions will likely mean a change in the electric bill?',
		answers: [
			'1-1-508.png',
			'1-2-508.png',
			'1-3-508.png',
			'1-4-508.png'
			],
		correct_answer_index: 3,
		insight: 'Joe doesn\'t actually read his electric bill. But his wife does. (Better duck, Joe.) She finds it at nvenergy.com<br />'
	},
	
	{
		title: 'Unplayed Question 2',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_title: '',
		learn_more_url: 'TEST',
		clue: 'Lisa in Las Vegas knows her phone carrier charges differently in daytime (On-Peak) than in evening (Off-Peak). Lisa goes online to see if her electric bill works the same way.',
		question: 'Which hours apply to the optional Time Of Use (TOU) plan:',
		answers: [
			'On-Peak hours',
			'Off-Peak hours',
			'Standard',
			'All of the above'
			],
		correct_answer_index: 3,
		insight: 'On the nvenergy.com bill calculator, Lisa sees her summer On-Peak rate is 57&cent; for every consumed energy unit (kWh). Her Off-Peak rate is just 8.2&cent;! All other hours are 10.9&cent;.'
	},
	
	{
		title: 'Unplayed Question 3',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_url: 'https://www.nvenergy.com/home/paymentbilling/equalpayment.cfm',
		clue: 'Jason from Lovelock loves things to be predictable. Seriously, if we gave away the answer to this question up front, Jason would be a happy man.',
		question: 'Why might your electricity bill be the exact same amount month after month?',
		answers: [
			'the weather doesn\'t change',
			'you only turn lights on at night',
			'you\'re on Equal Payment Plan',
			'you\'re in the movie Groundhog Day'
			],
		correct_answer_index: 2,
		insight: 'Jason stays predictably on budget with Equal Payment Plan. Being a creature of habit has its perks.'
	},
	
	{
		title: 'Unplayed Question 4',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'image',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_url: 'https://www.nvenergy.com/home/saveenergy/energytips.cfm',
		clue: '4-448.png',
		question: 'True or False? In the clue shown, the customer used LESS electricity in July 2011 than in July of the previous year.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 3,
		insight: 'The bar graph on your bill is a good way to spot how well you\'re saving energy. Melt down those bars and save money by using even just one of the tips at nvenergy.com<br />'
	},
	
	{
		title: 'Unplayed Question 5',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_title: '',
		learn_more_url: 'TEST',
		clue: 'Amber at UNLV is on a budget. Her dad\'s budget, to be precise. He asks his daughter to reduce her monthly power bill by $10.',
		question: 'What is an online bill calculator?',
		answers: [
			'A money-saving tool at nvenergy.com',
			'A money-saving tool on your bill',
			'A money-saving app for your desktop',
			'A money-saving desk for your laptop'
			],
		correct_answer_index: 0,
		insight: 'Amber played with the bill calculator until it showed her $10/mo less than her real bill. Now she knows how to get her dad that $10. Follow the "learn more" link and try it for yourself.'
	},
	
	{
		title: 'Unplayed Question 6',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_url: 'https://www.nvenergy.com/home/saveenergy/rebates/index.cfm',
		clue: 'Helen and Gracie from Henderson won\'t touch computers. But they love to compete for who can find the best rebates!',
		question: 'When paying your bill online, where do you find out if you qualify for rebates?',
		answers: [
			'type "rebate" in the search box at nvenergy.com',
			'reach for the phone and call NV Energy',
			'look for the link at the bottom of NVE\'s webpage',
			'all of the above'
			],
		correct_answer_index: 4,
		insight: 'Helen called by phone. But Gracie found more rebates by asking her grandson to go online. True, the ice cream bribe was a bit unfair. Visit nvenergy.com to see what he found.'
	},
	
	{
		title: 'Unplayed Question 7',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'image',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_url: 'https://www.nvenergy.com/home/customercare/understandyourbill.cfm',
		clue: '7-473.png',
		question: 'True or False? The Billing Usage column on your bill shows how many units of energy (kWh) your household consumed.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'Enter this Billing Usage number into your bill calculator at nvenergy.com. Manage the amount you\'re charged by managing the energy units (kWh) you consume.'
	},
	
	{
		title: 'Unplayed Question 8',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'image',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_url: 'http://www.energysavers.gov/tips/',
		clue: '179-518.png',
		question: 'True or False? In Nevada\'s Mojave Desert, the Sand Flibbert can live its entire life without drinking a drop of liquid.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 3,
		insight: 'Actually the Kangaroo Rat of Death Valley can go all its life without drinking. Now that\'s conservation. We suggest you hydrate. But try these tips: energysavers.gov/tips'
	},
	
	{
		title: 'Unplayed Question 9',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'image',
		category: 'How to Read Your Bill',
		learn_more_title: '',
		learn_more_url: '',
		clue: 'David from Duckwater pays $4/gal for gas, and 8&cent;/min to call home. But he doesn\'t know what rate he pays for each unit of energy (kWh) that his house consumes.',
		question: 'In the Electric Consumption row of your bill, which of the following would mean a rate of almost 11 cents per unit of energy (kWh)?',
		answers: [
			'200 kWH x .10209',
			'200 kWH x 11.029',
			'200 kWH x 110009',
			'200 KFC for $100.99'
			],
		correct_answer_index: 0,
		insight: '180-515.png'
	},
	
	{
		title: 'Unplayed Question 10',
		time: 'Today, 12:00pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		learn_more_url: '',
		clue: 'Jennifer from Tahoe is a conspiracy theorist. She is convinced that big brother is messing with her mind by fluctuating her bill monthly.',
		question: 'If your energy use routine never changed, why might your bill amount differ from month to month?',
		answers: [
			'The number of days in the month differs',
			'Rates adjust daily like stock prices',
			'Big Brother IS messing with our minds',
			'None of the above'
			],
		correct_answer_index: 0,
		insight: 'Jennifer may not solve the mystery of the Western calendar. But she can save on her bill by comparing her average DAILY consumption month to month.'
	}
];


/*
var questions = [
	{
		title: 'Unplayed Question 1',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
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
		title: 'Unplayed Question 1',
		time: 'Two days ago, 9:52am',
		question_type: 'text',
		answer_type: 'image',
		clue_type: 'text',
		insight_type: 'text',
		category: 'How to Read Your Bill',
		clue: 'New additions draw more power.',
		question: 'What are some reasons electricity bills might suddenly rise? ',
		answers: [
			'desktop_answer_2_1.png',
			'desktop_answer_2_2.png',
			'desktop_answer_2_3.png',
			'all_of_the_above.png'
			],
		correct_answer_index: 3,
		insight: 'One way to lower the cost of new appliances is to turn them off when not in use.'
	},
	
	{
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
		question: 'What part of your bill can illustrate conservation efforts over time?',
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
		question: 'The billing usage field tells you how much energy you have used in the billing cycle.',
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

];
*/