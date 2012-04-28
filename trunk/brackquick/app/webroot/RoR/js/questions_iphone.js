var questions = [
	{
		title: 'Unplayed Question 1',
		time: '12/03/12 10:16am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'It sweeps without a broom.',
		question: 'What product is easy to install, saves energy and lowers utility bills?',
		answers: [
			'Fan',
			'Door-sweep',
			'Window tint',
			'Swamp cooler'
		],
		correct_answer_index: 1,
		insight: 'Door-sweeps can be found at any home improvement store and keep cool air in and warm air out. They easily screw into the bottom of any door.'
	},
	{
		title: 'Unplayed Question 2',
		time: '12/03/12 11:42am',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'Only 15 percent of air leakage is through windows and doors.',
		question: 'Replacing windows are the only way to stop air leaks.',
		answers: [
			null,
			null,
			'True',
			'False'
		],
		correct_answer_index: 3,
		insight: 'Caulking small areas around the house, particularly the attic and foundation, will lower your heat loss and improve your bill, oftentimes more than replacing windows.'
	},
	{
		title: 'Unplayed Question 3',
		time: '12/03/12 12:02pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'If there\'s a hole, caulking will save energy and cost.',
		question: 'The top choices for caulking and weather stripping inside the home are:',
		answers: [
			'The attic access door.',
			'Electrical outlets and switches.',
			'Fixtures, pipes, electrical wires and anything that penetrates the ceiling or walls to the outside where air comes in and goes out.',
			'All of the above.'
			],
		correct_answer_index: 3,
		insight: 'Exterior caulking keeps the rain out but doesn\'t improve energy savings.<br />Source: Coloradoenergy.org'
	},
	{
		title: 'Unplayed Question 4',
		time: '12/03/12 1:33pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'image',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'child_safety_plugs.png',
		question: 'Using child safety plugs with an insulated backing will lower energy costs by preventing air leakage.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'Plugging the many small areas in your home where air leaks can add up to big savings in utility bills.'
	},
	{
		title: 'Unplayed Question 5',
		time: '12/03/12 2:47pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'WAP is the federal government\'s Weatherization Assistance Program',
		question: 'Qualified low-income families can save more than $400 per year by participating in the Weatherization Assistance Program.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'The U.S. Department of Energy\'s office of Energy Efficiency & Renewable Energy has many programs available to consumers that help save money and energy.'
	},
	/*{
		title: 'Unplayed Question 6',
		time: '12/03/12 2:47pm',
		question_type: 'text',
		answer_type: 'image',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'Start with the common areas where there are gaps for air to leak.',
		question: 'Which of the following [use images] are likely to be a source for energy loss (and higher utility bills):',
		answers: [
			'bg_answer_image_blank.png',
			'bg_answer_image_blank.png',
			'bg_answer_image_blank.png',
			'bg_answer_image_blank.png'
			],
		correct_answer_index: 3,
		insight: 'android-icon.png'
	},*/
	{
		title: 'Unplayed Question 7',
		time: '12/03/12 2:47pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'Leaks are a likely culprit.',
		question: 'One room that is more difficult to heat or cool than others could be a sign that:',
		answers: [
			'The furnace needs replacing.',
			'The air conditioner needs replacing.',
			'It\'s been more than a year since the filter was changed.',
			'The ductwork might be leaky, poorly insulated or otherwise inefficient.'
			],
		correct_answer_index: 3,
		insight: 'Many homeowners choose to hire a professional to inspect and repair ductwork.<br />Source:  Environmental Protection Agency'
	},
	{
		title: 'Unplayed Question 8',
		time: '12/03/12 2:47pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'A blow to put you in the know.',
		question: 'Which test is important for determining a home\'s air tightness?',
		answers: [
			'Blower door test.',
			'Candle test.',
			'Air tightness quiz.',
			'None of the above.'
			],
		correct_answer_index: 0,
		insight: 'Blower door tests are performed by professional energy auditors. Local government sustainability offices or utility companies are resources for finding a company that performs energy assessments.<br />Source: www.energysavers.gov'
	},
	{
		title: 'Unplayed Question 9',
		time: '12/03/12 2:47pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'It\'s a fantastic--and powerful--fan.',
		question: 'A blower door is a special fan that mounts onto an exterior door, pulling the air from the house as part of a test to locate unsealed cracks and openings.',
		answers: [
			null,
			null,
			'True',
			'False'
			],
		correct_answer_index: 2,
		insight: 'When hiring someone to perform a blower door test, it\'s important to choose an auditor who uses a calibrated blower door.<br />Source: www.energysavers.gov'
	},
	{
		title: 'Unplayed Question 10',
		time: '12/03/12 2:47pm',
		question_type: 'text',
		answer_type: 'text',
		clue_type: 'text',
		insight_type: 'text',
		category: 'Air Leaks',
		clue: 'Vinyl shines when it comes to weather-stripping.',
		question: 'Which weather-stripping material is considered the most durable and weather-resistant:',
		answers: [
			'Felt or foam',
			'Vinyl',
			'Metal',
			'All of the above.'
			],
		correct_answer_index: 1,
		insight: 'Choose a material appropriate for where it will be used.  Felt and foam are inexpensive. Vinyl is only slightly more expensive and is more durable and resistant to weather.'
	}
];