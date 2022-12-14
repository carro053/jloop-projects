<?php
if($game['Game']['has_icon'])
{
	$icon_src = $game['Game']['id'].'.png';
}else{
	$icon_src = 'default.png';
}
?>

<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui-1.8.19.custom.min.js"></script>
		<script type="text/javascript" src="/games/json_data/<?php echo $game['Game']['id']; ?>/<?php if(isset($snapshot)) { echo $snapshot; }else{ echo 0; } ?>/<?php if(isset($question['QuestionVersion']['id'])) { echo $question['QuestionVersion']['id']; }else{ echo 0; } ?>/data.js?t=<?php echo time(); ?>"></script>
		<script type="text/javascript">
			//alert( parseInt(location.hash.substr(1))  );
			var prize = false;
			var drawer_top;
			var drawer_bottom;
			var unplayedQuestionCount;
			
			var leaderboardWasDragged = false;
			
			var timerPosition = 0;
			var questionAnswered = false;
			var answerClicked;
			var readyPersonCount = answeredPersonCount = 0;
			var showAnswerImage = false;
			
			/*var correctImageCache = [];
			for(var index = 1; index < 101; index++) {
				var indexString = index.toString();
				while(indexString.length < 4) {
					indexString = "0" + indexString;
				}
				var image = new Image;
				image.src = '/img/animation/correct'+indexString+'.png';
				correctImageCache.push(image);
			}
			
			var incorrectImageCache = [];
			for(var index = 1; index < 101; index++) {
				var indexString = index.toString();
				while(indexString.length < 4) {
					indexString = "0" + indexString;
				}
				var image = new Image;
				image.src = '/img/animation/incorrect'+indexString+'.png';
				incorrectImageCache.push(image);
			}*/
			var imageCache = [];
			var imageC = new Image;
			imageC.src = '/img/animation/correct0065.png';
			imageCache.push(imageC);
			var imageI = new Image;
			imageI.src = '/img/animation/incorrect0065.png';
			imageCache.push(imageI);
			
			var audioCheck = !($.browser.msie); 
			if(audioCheck) {
				var clueAudio = new Audio('/audio/ROR_ringtoneAB_combined.ogg');
				var correctAudio = new Audio('/audio/correct.ogg');
				var incorrectAudio = new Audio('/audio/incorrect.ogg');
				var timeout;
			}
			
			var question;
			
			function titleFade() {
				$('#title').fadeOut(1000, function() { $('#title').html(''); });
			}
			
			function reset() {
				if(question) {
					leaderboardWasDragged = false;
					questionAnswered = false;
					answerClicked = null;
					showAnswerImage = false;
					
					$('#content').css('left', 0);
					$('#app').css('background-image', 'url(/img/bg_game_offline.png)');
					$('#meter').appendTo('#clue');
					$('#meter').css('marginLeft', 0);
					$('#meter').show();
					$('#clue').css('background-image', 'url(/img/btn_clue_blue.png)');
					$('#question').css('background-image', 'url(/img/btn_question_black.png)');
					$('#insight').css('background-image', 'url(/img/btn_insight_black.png)');
					$('#subContentClue').show();
					$('#subContentInsight').show();
					$('#drawer').hide();
					$('#drawer').css('top', '468px');
					$('#category').html(question.category);
					switch(question.clue_type) {
						case 'text':
							$('#contentClue').css('background-image', 'url(/img/bg_white_textbox.png)');
							$('#textClue').html(question.clue);
							break;
						case 'image':
							$('#textClue').html('');
							$('#contentClue').css('background-image', 'url(/img/clues/'+question.clue+')');
							break;
					}
					switch(question.question_type) {
						case 'text':
							$('#contentQuestion').css('background-image', 'url(/img/bg_white_textbox.png)');
							$('#textQuestion').html(question.question);
							break;
						case 'image':
							$('#textQuestion').html('');
							$('#contentQuestion').css('background-image', 'url(/img/questions/'+question.question+')');
							break;
					}
					switch(question.insight_type) {
						case 'text':
							$('#contentInsight').css('background-image', 'url(/img/bg_white_textbox.png)');
							$('#textInsight').html(question.insight);
							break;
						case 'image':
							$('#textInsight').html('');
							$('#contentInsight').css('background-image', 'url(/img/insights/'+question.insight+')');
							break;
					}
					for(var i in question.answers) {
						$('#answer'+i+' img').hide();
						if(question.answers[i]) {
							$('#answer'+i).show();
							switch(question.answer_type) {
								case 'text':
									$('#answer'+i).css('background-image', 'url(/img/bg_answer_grey.png)');
									$('#answer'+i+' p').html(question.answers[i]);
									break;
								case 'image':
									$('#answer'+i+' p').html('');
									$('#answer'+i).css('background-image', 'url(/img/answers/'+question.answers[i]+')');
									break;
							}
						}
						else
							$('#answer'+i).hide();
					}
				}else{
					alert('load question first');
				}
			}
			
			function loadQuestion(index) {
				if(questions[index]) {
					question = questions[index];
				}else{
					alert('question at index '+index+' not found');
				}
				
				var properties = {left: '0px'};
				var options = {
					duration: 500,
					easing: 'swing',
					complete: function() {
						reset();
						$('#timelineQuestion'+index).remove();
					}
				};
				$('#app').animate(properties, options);
				reset();
				clueTimer();
				unplayedQuestionCount--;
				$('#unplayedQuestionCount').html(unplayedQuestionCount);
			}
			
			function clueTimer() {
				if(audioCheck) {
					timeout = setTimeout(function() {clueAudio.play();}, <?php if($preview_timers) { echo '100'; }else{ echo '17500'; } ?>); //100
				}
				var properties = {marginLeft: '74px'};
				var options = {
					duration: <?php if($preview_timers) { echo '3000'; }else{ echo '20000'; } ?>, //3000
					easing: 'linear',
					complete: function() {
						$('#meter').appendTo('#question');
						$('#meter').css('marginLeft', 0);
						questionTimer();
					}
				};
				timerPosition = 1;
				$('#meter').animate(properties, options);
			}
			
			function questionTimer() {
				$('#subContentClue').hide();
				timerPosition = 2;
				slideContent(2);
				$('#question').css('background-image', 'url(/img/btn_question_blue.png)');
				var properties = {marginLeft: '131px'};
				var options = {
					duration: <?php if($preview_timers) { echo '3000'; }else{ echo '20000'; } ?>, //3000
					easing: 'linear',
					complete: function() {
						$('#meter').appendTo('#insight');
						$('#meter').css('marginLeft', 0);
						insightTimer();
					}
				};
				$('#meter').animate(properties, options);
			}
			
			function insightTimer() {
				timerPosition = 3;
				slideContent(3);
				$('#insight').css('background-image', 'url(/img/btn_insight_blue.png)');
				$('#subContentInsight').css('background-image', 'url(/img/bg_sub_compiling.gif)');
				$('.personCounter').hide();
				var properties = {marginLeft: '74px'};
				var options = {
					duration: <?php if($preview_timers) { echo '1000'; }else{ echo '10000'; } ?>, //1000
					easing: 'linear',
					complete: function() {
						loadAnswerStates();
						showAnswerImage = true;
						timerPosition = 0;
						slideContent(2);
						$('#meter').hide();
						$('#subContentInsight').hide();
					}
				};
				$('#meter').animate(properties, options);
			}
			
			function slideContent(position) {
				var properties = false;
				switch(timerPosition) {
					case 1:
						properties = {left: '0px'};
						break;
					case 2:
						if(position == 1 && !questionAnswered) {
							properties = {left: '0px'};
							$('#clue').css('background-image', 'url(/img/btn_clue_blue.png)');
							$('#question').css('background-image', 'url(/img/btn_question_grey.png)');
							$('#insight').css('background-image', 'url(/img/btn_insight_black.png)');
						}
						if(position == 2 && !questionAnswered) {
							properties = {left: '-320px'};
							$('#clue').css('background-image', 'url(/img/btn_clue_grey.png)');
							$('#question').css('background-image', 'url(/img/btn_question_blue.png)');
							$('#insight').css('background-image', 'url(/img/btn_insight_black.png)');
						}
						if(position == 3 && questionAnswered) {
							properties = {left: '-640px'};
							$('#clue').css('background-image', 'url(/img/btn_clue_black.png)');
							$('#question').css('background-image', 'url(/img/btn_question_black.png)');
							$('#insight').css('background-image', 'url(/img/btn_insight_blue.png)');
						}
						break;
					case 3:
						properties = {left: '-640px'};
						$('#clue').css('background-image', 'url(/img/btn_clue_black.png)');
						$('#question').css('background-image', 'url(/img/btn_question_black.png)');
						$('#insight').css('background-image', 'url(/img/btn_insight_blue.png)');
						break;
					default:
						if(position == 1) {
							properties = {left: '0px'};
							$('#clue').css('background-image', 'url(/img/btn_clue_blue.png)');
							$('#question').css('background-image', 'url(/img/btn_question_grey.png)');
							$('#insight').css('background-image', 'url(/img/btn_insight_grey.png)');
						}
						if(position == 2) {
							properties = {left: '-320px'};
							$('#clue').css('background-image', 'url(/img/btn_clue_grey.png)');
							$('#question').css('background-image', 'url(/img/btn_question_blue.png)');
							$('#insight').css('background-image', 'url(/img/btn_insight_grey.png)');
						}
						if(position == 3) {
							properties = {left: '-640px'};
							$('#clue').css('background-image', 'url(/img/btn_clue_grey.png)');
							$('#question').css('background-image', 'url(/img/btn_question_grey.png)');
							$('#insight').css('background-image', 'url(/img/btn_insight_blue.png)');
						}
						break;
				}
				var options = {
					duration: 500,
					easing: 'swing',
					complete: function() {
						if(showAnswerImage)
							animateAnswerImage();
					}
				};
				if(properties)
					if(properties.left != $('#content').css('left'))
						$('#content').animate(properties, options);
			}
			
			function imReady() {
				if(audioCheck) {
					clearTimeout(timeout);
				}
				$('#meter').stop();
				$('#meter').appendTo('#question');
				$('#meter').css('marginLeft', 0);
				questionTimer();
			}
			
			function answerQuestion(answerIndex) {
				if(!questionAnswered && timerPosition) {
					questionAnswered = true;
					answerClicked = answerIndex;
					$('#meter').stop();
					$('#meter').appendTo('#insight');
					$('#meter').css('marginLeft', 0);
					insightTimer();
				}
			}
			
			function loadAnswerStates() {
				
				$('#drawer').html('');
				
				switch(question.answer_type) {
					case 'text':
						if(answerClicked == question.correct_answer_index) {
							$('#answer'+answerClicked).css('background-image', 'url(/img/bg_answer_green.png)');
							if(question.learn_more_url)
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_correct_learn_more.png)');
							else
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_correct.png)');
						}else{
							$('#answer'+question.correct_answer_index).css('background-image', 'url(/img/bg_answer_green.png)');
							$('#answer'+answerClicked).css('background-image', 'url(/img/bg_answer_red.png)');
							if(question.learn_more_url)
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_incorrect_learn_more.png)');
							else
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_incorrect.png)');
						}
						break;
					case 'image':
						if(answerClicked == question.correct_answer_index) {
							//$('#answer'+answerClicked).css('background-image', 'url(/img/bg_answer_image_green.png)');
							$('#answer'+answerClicked+' img').attr('src', '/img/bg_answer_image_green.png');
							$('#answer'+answerClicked+' img').show();
							if(question.learn_more_url)
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_correct_learn_more.png)');
							else
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_correct.png)');
						}else{
							//$('#answer'+question.correct_answer_index).css('background-image', 'url(/img/bg_answer_image_green.png)');
							//$('#answer'+answerClicked).css('background-image', 'url(/img/bg_answer_image_red.png)');
							$('#answer'+question.correct_answer_index+' img').attr('src', '/img/bg_answer_image_green.png');
							$('#answer'+question.correct_answer_index+' img').show();
							$('#answer'+answerClicked+' img').attr('src', '/img/bg_answer_image_red.png');
							$('#answer'+answerClicked+' img').show();
							if(question.learn_more_url)
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_incorrect_learn_more.png)');
							else
								$('#drawer').css('background-image', 'url(/img/bg_leaderboard_drawer_incorrect.png)');
						}
						break;
				}
				if(answerClicked == question.correct_answer_index && question.prize != false && question.prize != null)
				{
					drawer_top = '120';
					drawer_bottom = '468';
					$('#drawer').css('background-image', 'url(/img/prizes/bg.png)');
					$('#drawer').html('<img class="prize-image" src="/img/prizes/'+question.prize+'" /><span class="prize-text">'+question.prize_text+'</span>');
				}else if (question.learn_more_url) {
					drawer_top = '364';
					drawer_bottom = '468';
				}else{
					drawer_top = '408';
					drawer_bottom = '468';
					$('#drawer').html('');
				}
				
				//Learn More button
				if(question.learn_more_url)
				{
					$('#drawer').append('<div id="learnMoreButton" onclick="learnMoreLoadScreen();"></div>');
					$('#learnMoreTitle').html(question.learn_more_title);
				}
			}
			
			function learnMoreLoadScreen()
			{
				$('#learnMoreGameTitle').html(question.category);
				$('#learnMoreTitle').html(question.learn_more_title);
				$('#learnMoreLoading').fadeIn(1000);
				setTimeout(function() { 
					//window.open(question.learn_more_url);
					$('#learnMoreLoading').fadeOut(1000);
					
					$('#browser').show();
					var properties = {left: '0px'};
					var options = {
						duration: 500,
						easing: 'swing',
						complete: function() {
							
						}
					};
					$('#browser').animate(properties, options);
				}, 2000);
			}
			
			function closeBrowser()
			{
				var properties = {left: '320px'};
				var options = {
					duration: 500,
					easing: 'swing',
					complete: function() {
						$('#browser').hide();
					}
				};
				$('#browser').animate(properties, options);
			}
			
			function animateAnswerImage() {
				$('#app').css('background-image', 'url(/img/bg_game_back.png)');
				
				if(answerClicked == question.correct_answer_index) {
					if(audioCheck) {
						var audio = correctAudio;
					}
					//var imageCache = correctImageCache;
					$('#answerAnimation').attr('src', '/img/animation/correct0065.png');
				}else{
					if(audioCheck) {
						var audio = incorrectAudio;
					}
					//var imageCache = incorrectImageCache;
					$('#answerAnimation').attr('src', '/img/animation/incorrect0065.png');
				}
				
				if(audioCheck) {
					audio.play();
				}
				
				showAnswerImage = false;
				$('#answerAnimation').show();
				
				var properties = {top: drawer_top+'px'};
				var options = {
					duration: 1000,
					easing: 'swing',
					complete: function() {
						if(!leaderboardWasDragged && drawer_top != '120') {
							setTimeout(function() {
								$('#drawer').animate(
									{top: drawer_bottom+'px'},
									{duration: 1000, easing: 'swing'}
								);
							}, 2000);
						}
					}
				};
				
				$('#answerAnimation').fadeOut(3000, function() {
					$('#drawer').show();
					$('#drawer').animate(properties, options);
				});
				
				/*var index = 0;
				var interval = setInterval(function() {
					if(index >= 99) {
						clearInterval(interval);
						$('#drawer').show();
						$('#drawer').animate(properties, options);
						$('#answerAnimation').hide();
					}
					$('#answerAnimation').attr('src', imageCache[index].src);
					index++;
				}, 50);*/
			}
			
			$(function() {
				unplayedQuestionCount = questions.length;
				$('#unplayedQuestionCount').html(unplayedQuestionCount);
				$(document).disableSelection();
				$('#clue').click(function() {slideContent(1);});
				$('#question').click(function() {slideContent(2);});
				$('#insight').click(function() {slideContent(3);});
				$('#subContentClue').click(function() {imReady();});
				$('#backButton').click(function() {
					if(unplayedQuestionCount > 0) {
						var properties = {left: '320px'};
						var options = {
							duration: 500,
							easing: 'swing'
						};
						if(timerPosition == 0)
							$('#app').animate(properties, options);
					} else {
						$('#title').fadeIn(1000, function() { window.location = '2-1.html'; });
					}
				});
				$('#answer0').click(function() {answerQuestion(0);});
				$('#answer1').click(function() {answerQuestion(1);});
				$('#answer2').click(function() {answerQuestion(2);});
				$('#answer3').click(function() {answerQuestion(3);});
				
				$('#answer0, #answer1, #answer2, #answer3').mousedown(function() {
					switch(question.answer_type) {
						case 'text':
							if(!questionAnswered)
								$(this).css('background-image', 'url(/img/bg_answer_grey_selected.png)');
							break;
						case 'image':
							//if(!questionAnswered)
							//	$(this).css('background-image', 'url(/img/bg_answer_image_blank_clicked.png)');
							break;
					}
				});
				$('#answer0, #answer1, #answer2, #answer3').mouseup(function() {
					switch(question.answer_type) {
						case 'text':
							if(!questionAnswered)
								$(this).css('background-image', 'url(/img/bg_answer_grey.png)');
							break;
						case 'image':
							//if(!questionAnswered)
							//	$(this).css('background-image', 'url(/img/bg_answer_image_blank.png)');
							break;
					}
				});
				
				$("#drawer").draggable({containment: [0, 220, 0, 568], axis: 'y'}); //DESKTOP
				/* $("#drawer").draggable({containment: [0, 265, 0, 613], axis: 'y'}); */ //IPHONE
				$('#drawer').mousedown(function() {leaderboardWasDragged = true;});
				$('#drawer').mouseup(function() {
					if(parseInt($(this).css('top')) < 294) {
						var properties = {top: '120px'};
					} else {
						var properties = {top: '468px'};
					}
					var options = {
						duration: 350,
						easing: 'swing'
					};
					$(this).animate(properties, options);
				});
				
				$('#timelineButton').click(function() {
					var properties = {top: '0px'};
					var options = {
						duration: 350,
						easing: 'swing'
					};
					$('#timelineDrawer').animate(properties, options);
				});
				var properties = {top: '364px'};
				var options = {
					duration: 0,
					easing: 'swing'
				};
				$("#timelineDrawer").animate(properties, options);
				$("#timelineDrawer").draggable({containment: 'parent', axis: 'y'});
				$('#timelineDrawer').mouseup(function() {
					if(parseInt($(this).css('top')) < 182) {
						var properties = {top: '0px'};
					} else {
						var properties = {top: '364px'};
					}
					var options = {
						duration: 350,
						easing: 'swing'
					};
					$(this).animate(properties, options);
				});
				
				//loadQuestion(parseInt(location.hash.substr(1)));
				//reset();
				//clueTimer();
				
				for(var i in questions) {
					$('#timelineDrawer').append('<div id="timelineQuestion'+i+'" onclick="loadQuestion('+i+');" class="unplayedQuestion"><img src="/img/game_icons/<?php echo $icon_src; ?>" style="float: left; margin-top: -7px; margin-left: -48px;" width="48" height="42" /><p class="unplayedQuestionTitle">'+questions[i]['title']+'</p><p class="unplayedQuestionTime">'+questions[i]['time']+'</p></div>');
				}
				if(questions.length == 1) loadQuestion(0);
			});
		</script>
		<style type="text/css">
			* {
				margin: 0;
				padding: 0;
			}
			
			body {
				background: #aaa;
				/*background-color: #000;
				background-image: url(/img/bg_2.png); /* DESKTOP */
				/* background-image: url(/img/iphone_ringorang.png); */ /* IPHONE */
				background-position: 0 0;
				background-repeat: no-repeat;
				overflow: hidden;
			}
			
			#shadow {
				position: absolute;
				left: 458px;
				top: 60px;
				z-index: -1;
			}
			
			#timeline {
				width: 320px;
				height: 486px;
				background-image: url(/img/bg_desktop_timeline.png); /* DESKTOP */
				/* background-image: url(/img/bg_app_timeline_ios.png); */ /* IPHONE */
				background-repeat: no-repeat;
				background-position: 0 0;
				/* margin: 145px 488px; */ /* IPHONE */
				margin: 100px 488px; /* DESKTOP */
				overflow: hidden;
			}
			
			#app {
				width: 320px;
				height: 486px;
				background-image: url(/img/bg_game_offline.png);
				background-repeat: no-repeat;
				background-position: bottom;
				overflow: hidden;
				position: relative;
				left: 320px;
				z-index: 5;
			}
			
			#backButton {
				position: absolute;
				width: 40px;
				height: 40px;
				margin: 26px 0 0 10px;
			}
			
			#buttons {
				width: 369px;
				height: 52px;
				display: block;
				margin: 140px 0 0 5px;
			}
			
			#category {
				margin: -12px 0 0 -5px;
				position: absolute;
				text-align: center;
				width: 320px;
				color: #fff;
				font-family: Helvetica;
				font-size: 12px;
				font-weight: bold;
			}
			
			#clue {
				width: 83px;
				height: 52px;
				background: url(/img/btn_clue_blue.png);
				float: left;
				overflow: hidden;
			}
			
			#question {
				width: 143px;
				height: 52px;
				background: url(/img/btn_question_black.png);
				float: left;
				overflow: hidden;
			}
			
			#insight {
				width: 84px;
				height: 52px;
				background: url(/img/btn_insight_black.png);
				float: left;
				overflow: hidden;
			}
			
			#meter {
				width: 312px;
				height: 12px;
				background: url(/img/red_meter.png);
				margin: 34px 0 0 0;
			}
			
			#content {
				width: 1500px;
				height: 294px;
				position: relative;
				left: 0px;
			}
			
			#contentClue {
				width: 320px;
				height: 296px;
				background-image: url(/img/bg_white_textbox.png);
				background-repeat: no-repeat;
				background-position: -9px 0;
				float: left
			}
			
			#contentQuestion {
				width: 320px;
				height: 296px;
				background-image: url(/img/bg_white_textbox.png);
				background-repeat: no-repeat;
				background-position: -9px 0;
				float: left
			}
			
			#contentInsight {
				width: 320px;
				height: 296px;
				background-image: url(/img/bg_white_textbox.png);
				background-repeat: no-repeat;
				background-position: -9px 0;
				float: left
			}
			
			#textClue {
				width: 272px;
				height: 87px;
				padding: 18px 24px 0;
				position: absolute;
			}
			
			#textQuestion {
				width: 272px;
				height: 87px;
				padding: 18px 24px 0;
				position: absolute;
			}
			
			#textInsight {
				width: 272px;
				height: 87px;
				padding: 18px 24px 0;
				position: absolute;
			}
			
			p.textbox {
				color: #000;
				font-family: Helvetica;
				font-size: 13px;
				font-weight: lighter;
				line-height: 18px;
			}
			
			p.textbox img {
				display: block;
				margin: -5px auto;
			}
			
			#answer0 {
				width: 155px;
				height: 96px;
				background-image: url(/img/bg_answer_grey.png);
				background-repeat: no-repeat;
				background-position: 0px 0;
				position: absolute;
				margin: 107px 0 0 6px;
				display: table;
			}
			
			#answer1 {
				width: 155px;
				height: 96px;
				background-image: url(/img/bg_answer_grey.png);
				background-repeat: no-repeat;
				background-position: 0px 0;
				position: absolute;
				margin: 107px 0 0 158px;
				display: table;
			}
			
			#answer2 {
				width: 155px;
				height: 96px;
				background-image: url(/img/bg_answer_grey.png);
				background-repeat: no-repeat;
				background-position: 0px 0;
				position: absolute;
				margin: 197px 0 0 6px;
				display: table;
			}
			
			#answer3 {
				width: 155px;
				height: 96px;
				background-image: url(/img/bg_answer_grey.png);
				background-repeat: no-repeat;
				background-position: 0px 0;
				position: absolute;
				margin: 197px 0 0 158px;
				display: table;
			}
			
			#answer0 img{
				position: absolute;
				margin: 2px;
				display: none;
			}
			
			#answer1 img{
				position: absolute;
				margin: 2px;
				display: none;
			}
			
			#answer2 img{
				position: absolute;
				margin: 2px;
				display: none;
			}
			
			#answer3 img{
				position: absolute;
				margin: 2px;
				display: none;
			}
			
			p.answer {
				color: #fff;
				font-family: Helvetica;
				font-size: 14px;
				font-weight: bold;
				display: table-cell;
				text-align: center;
				vertical-align: middle;
				text-shadow: 0px -1px #000;
				padding: 0 15px;
			}
			
			#subContentClue {
				width: 320px;
				height: 70px;
				background-image: url(/img/bg_sub_im_ready.png);
				margin: 228px 0 0 0;
			}
			
			#subContentInsight {
				width: 320px;
				height: 51px;
				background-image: url(/img/bg_sub_answered.png);
				background-repeat: no-repeat;
				margin: 243px 0 0 0;
			}
			
			.personCounter {
				width: 60px;
				color: #fff;
				padding: 15px 0 0 20px;
				display: inline-block;
				text-align: right;
			}
			
			#answerAnimation {
				width: 320px;
				height: 502px;
				position: relative;
				top: -167px;
				display: none;
			}
			
			/*#drawerParent {
				width: 320px;
				height: 712px;
				position: relative;
				top: -364px;
				display: none;
			}*/
			
			#drawer {
				width: 320px;
				height: 365px;
				background: url(/img/bg_leaderboard_drawer_correct.png);
				position: absolute;
				top: 468px;
				display: none;
			}
			
			#learnMoreButton {
				width: 280px;
				height: 30px;
				position: absolute;
				top: 28px;
				left: 18px
			}
			
			#learnMoreLoading {
				width: 320px;
				height: 365px;
				z-index: 5;
				position: relative;
				top: 120px;
				background: url(/img/learn_more_loading.png);
				display: none;
			}
			
			#browser {
				width: 320px;
				height: 462px;
				z-index: 10;
				position: relative;
				background: url(/img/browser.png);
				top: 24px;
				left: 320px;
				display: none;
			}
			
			#learnMoreGameTitle {
				color: gray;
				text-align: center;
				padding-top: 3px;
				font-family: sans-serif;
				font-weight: bold;
				font-size: 12px;
			}
			
			#learnMoreTitle {
				color: gray;
				text-align: center;
				padding-top: 50px;
				font-family: sans-serif;
				font-weight: bold;
				font-size: 14px;
			}
			
			#loadingGif {
				padding-left: 66px;
				padding-top: 12px;
			}
			
			#timelineButton {
				width: 320px;
				height: 51px;
				background: url(/img/time_line_button.png);
				position: absolute;
				margin: 314px 0 0 0;
			}
			
			#timelineDrawerParent {
				width: 320px;
				height: 730px;
				position: relative;
				top: -361px;
			}
			
			#timelineDrawer {
				width: 300px;
				height: 346px;
				background: url(/img/time_line_drawer.png);
				position: relative;
				top: 0px;
				padding: 20px 10px 0;
			}
			
			.unplayedQuestion {
				width: 244px;
				height: 40px;
				background: url(/img/desktop_drawer_item.png);
				margin-top: 1px;
				padding: 10px 0 0 56px;
			}
			
			.unplayedQuestionTitle {
				color: #1A4254;
				font-family: Helvetica;
				font-size: 14px;
				font-weight: bold;
			}
			
			.unplayedQuestionTime {
				color: #888;
				font-family: Helvetica;
				font-size: 12px;
			}
			
			#unplayedQuestionCount {
				display: block;
				width: 20px;
				height: 10px;
				position: absolute;
				color: #fff;
				font-family: Helvetica;
				font-size: 12px;
				font-weight: bold;
				margin-left: 86px;
				margin-top: 17px;
				text-align: center;
			}
			
			#title {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #000;
				color: #fff;
				text-align: center;
				z-index: 20;
			}
			#title h1 {
				margin: 200px 0 0 0;
			}
			#title h2 {
				margin: 80px 0 0 0;
			}
			.prize-image {
				margin-left: 37px;
				margin-top: 68px;
				display: block;
			}
			.prize-text {
				display: block;
				width: 235px;
				margin-left: 39px;
				margin-top: 11px;
				font-size: 11px;
				font-family: Helvetica;
				color: 
				#666;
				text-align: left;
				line-height: 14px;
			}
		</style>
	</head>
	<body>
		<img id="shadow" src="/img/shadow.png" /> <!-- DESKTOP -->
		<div id="timeline">
			<div id="app">
				<div id="browser" onclick="closeBrowser();"></div>
				<div id="learnMoreLoading">
					<p id="learnMoreGameTitle">Game Title</p>
					<h2 id="learnMoreTitle">Learn More About Stuff!</h2>
					<img id="loadingGif" src="/img/loading_learn.gif" />
				</div>
				<div id="backButton"></div>
				<div id="buttons">
					<p id="category"></p>
					<div id="clue">
						<div id="meter"></div>
					</div>
					<div id="question"></div>
					<div id="insight"></div>
				</div>
				<div id="content">
					<div id="contentClue">
						<p id="textClue" class="textbox"></p>
						<div id="subContentClue"></div>
					</div>
					<div id="contentQuestion">
						<p id="textQuestion" class="textbox"></p>
						<div id="answer0"><img src="/img/bg_answer_image_green.png" /><p class="answer"></p></div>
						<div id="answer1"><img src="/img/bg_answer_image_green.png" /><p class="answer"></p></div>
						<div id="answer2"><img src="/img/bg_answer_image_green.png" /><p class="answer"></p></div>
						<div id="answer3"><img src="/img/bg_answer_image_green.png" /><p class="answer"></p></div>
						<img id="answerAnimation" src="/img/animation/incorrect0001.png" />
					</div>
					<div id="contentInsight">
						<p id="textInsight" class="textbox"></p>
						<div id="subContentInsight"></div>
					</div>
				</div>
				<div id="drawer"></div>
			</div>
			<div id="timelineDrawerParent">
				<img src="/img/game_icons/<?php echo $icon_src; ?>" style="position:absolute; top:9px; left:16px; width:36px; height:36px;" />
				<img src="/img/game_icons/<?php echo $icon_src; ?>" style="position:absolute; top:139px; left:16px; width:36px; height:36px;" />
				<img src="/img/game_icons/<?php echo $icon_src; ?>" style="position:absolute; top:221px; left:16px; width:36px; height:36px;" />
				<img src="/img/game_icons/<?php echo $icon_src; ?>" style="position:absolute; top:270px; left:16px; width:36px; height:36px;" />
				<div id="timelineButton">
					<p id="unplayedQuestionCount">8</p>
				</div>
				<div id="timelineDrawer"></div>
			</div>
		</div>
		<?php /*<div id="title" onclick="titleFade();">
			<h1>Wed-Fri Gameplay on the Desktop</h1>
		</div> */ ?>
	</body>
</html>