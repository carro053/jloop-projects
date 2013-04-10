//
//  ViewOnlineMissionViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/19/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "ViewOnlineMissionViewController.h"
#import "SBJson.h"
#import "UIDevice+IdentifierAddition.h"
#import "ViewOnlineMissionCell.h"
#import "LGViewHUD.h"
#import "Twitter/Twitter.h"

@interface ViewOnlineMissionViewController ()

@end
double fastestTime;
double mostFuel;
double yourFastestTime;
double yourMostFuel;
int fastestTimeId;
int mostFuelId;
int yourFastestTimeId;
int yourMostFuelId;
int yourFastestTimePlacement;
int yourMostFuelPlacement;
int yourVote;
int yourAccountId;
NSArray *fastestTimes;
NSArray *mostFuels;
NSString *theTitle;
NSString *madeBy;
int totalFuel;
LGViewHUD* ViewOnlineMissionHud;
NSArray *onlineFilePaths;
NSString *onlineFileDirectory;
bool featured;

@implementation ViewOnlineMissionViewController
@synthesize missionTitle;
@synthesize minusOne;
@synthesize plusOne;
@synthesize solutionList;
@synthesize mission_id;
@synthesize playMissionViewController = _playMissionViewController;
@synthesize viewMissionSolutionViewController = _viewMissionSolutionViewController;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    self.mission_id = missionId;
    if (self) {
        onlineFilePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        onlineFileDirectory = [[NSString alloc] initWithFormat:@"%@",[onlineFilePaths objectAtIndex:0]];
        [TestFlight passCheckpoint:[NSString stringWithFormat:@"View Online Mission:%d",missionId]];
    }
    return self;
}

- (void)loadPuzzleTimes {
    NSDictionary *puzzleTimes = [self getPuzzleTimes];
    theTitle = [puzzleTimes objectForKey:@"title"];
    yourVote = 0;
    fastestTime = [[puzzleTimes objectForKey:@"fastest_time"] doubleValue];
    fastestTimeId = [[puzzleTimes objectForKey:@"fastest_time_id"] intValue];
    mostFuel = [[puzzleTimes objectForKey:@"most_fuel_remaining"] doubleValue];
    mostFuelId = [[puzzleTimes objectForKey:@"most_fuel_id"] intValue];
    yourFastestTime = [[puzzleTimes objectForKey:@"your_fastest_time"] doubleValue];
    yourFastestTimeId = [[puzzleTimes objectForKey:@"your_fastest_time_id"] intValue];
    yourMostFuel = [[puzzleTimes objectForKey:@"your_most_fuel"] doubleValue];
    yourMostFuelId = [[puzzleTimes objectForKey:@"your_most_fuel_id"] intValue];
    yourVote = [[puzzleTimes objectForKey:@"vote"] intValue];
    madeBy = [puzzleTimes objectForKey:@"made_by"];
    totalFuel = [[puzzleTimes objectForKey:@"total_fuel"] intValue];
    yourAccountId = [[puzzleTimes objectForKey:@"your_account_id"] intValue];
    fastestTimes = [[NSArray alloc] initWithArray:[puzzleTimes objectForKey:@"fastest_times"]];
    mostFuels = [[NSArray alloc] initWithArray:[puzzleTimes objectForKey:@"most_fuels"]];
    yourMostFuelPlacement = [[puzzleTimes objectForKey:@"your_most_fuel_placement"] intValue];
    yourFastestTimePlacement = [[puzzleTimes objectForKey:@"your_fastest_time_placement"] intValue];
    featured = [[puzzleTimes objectForKey:@"featured"] boolValue];
}

- (NSString *)stringWithUrl:(NSURL *)url
{
	NSURLRequest *urlRequest = [NSURLRequest requestWithURL:url
                                                cachePolicy:NSURLRequestReloadIgnoringCacheData
                                            timeoutInterval:5];
    // Fetch the JSON response
	NSData *urlData;
	NSURLResponse *response;
	NSError *error;
    
	// Make synchronous request
	urlData = [NSURLConnection sendSynchronousRequest:urlRequest
                                    returningResponse:&response
                                                error:&error];
    
 	// Construct a String around the Data from the response
	return [[NSString alloc] initWithData:urlData encoding:NSUTF8StringEncoding];
}

- (id) objectWithUrl:(NSURL *)url
{
	SBJsonParser *jsonParser = [SBJsonParser new];
	NSString *jsonString = [self stringWithUrl:url];
	// Parse the JSON into an Object
	return [jsonParser objectWithString:jsonString error:NULL];
}

- (NSDictionary *) getPuzzleTimes
{
    NSString *deviceUDID = [[UIDevice currentDevice] uniqueGlobalDeviceIdentifier];
	id response = [self objectWithUrl:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/puzzles/getPuzzleTimes/%d/%@",self.mission_id,deviceUDID]]];
	NSDictionary *feed = (NSDictionary *)response;
	return feed;
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    int sections = 0;
    if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
    {
        sections = 4;
    }else if([fastestTimes count] > 0)
    {
        sections = 3;
    }else{
        sections = 1;
    }
    if(featured)
        sections++;
    return sections;
}

- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section {
    if(featured) {
        if(section == 0)
        {
            return @"FEATURED MISSION";
        }
        if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
        {
            if(section == 1)
            {
                return [NSString stringWithFormat:@"Mission: %@",theTitle];
            }else if(section == 2)
            {
                return @"Your Solutions";
            }else if(section == 3)
            {
                return @"Left Over Fuel";
            }else if(section == 4)
            {
                return @"Fastest Time";
            }
        }else if(mostFuel > 0.0)
        {
            if(section == 1)
            {
                return [NSString stringWithFormat:@"Mission: %@",theTitle];
            }else if(section == 2)
            {
                return @"Left Over Fuel";
            }else if(section == 3)
            {
                return @"Fastest Time";
            }
        }
    }else{
        if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
        {
            if(section == 0)
            {
                return [NSString stringWithFormat:@"Mission: %@",theTitle];
            }else if(section == 1)
            {
                return @"Your Solutions";
            }else if(section == 2)
            {
                return @"Left Over Fuel";
            }else if(section == 3)
            {
                return @"Fastest Time";
            }
        }else if(mostFuel > 0.0)
        {
            if(section == 0)
            {
                return [NSString stringWithFormat:@"Mission: %@",theTitle];
            }else if(section == 1)
            {
                return @"Left Over Fuel";
            }else if(section == 2)
            {
                return @"Fastest Time";
            }
        }else{
            return [NSString stringWithFormat:@"Mission: %@",theTitle];
        }
    }
    return @"";
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    if(featured)
    {
        if(section == 0)
        {
            return 2;
        }else if(section == 1)
        {
            int rows = 2;
            if(mostFuel == 0)
                rows++;
            return rows;
        }else if(section == 2)
        {
            if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
            {
                return 2;
            }else{
                return [mostFuels count];
            }
        }else if(section == 3)
        {
            if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
            {
                return [mostFuels count];
            }else{
                return [fastestTimes count];
            }
        }else if(section == 4)
        {
            return [fastestTimes count];
        }
    }else{
        if(section == 0)
        {
            int rows = 2;
            if(mostFuel == 0)
                rows++;
            return rows;
        }else if(section == 1)
        {
            if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
            {
                return 2;
            }else{
                return [mostFuels count];
            }
        }else if(section == 2)
        {
            if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
            {
                return [mostFuels count];
            }else{
                return [fastestTimes count];
            }
        }else if(section == 3)
        {
            return [fastestTimes count];
        }
    }
    return 2;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    int indexInt = indexPath.row;
    int indexSection = indexPath.section;
    static NSString *CellIdentifier = @"ViewOnlineMissionCell";
    
    ViewOnlineMissionCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *topLevelObjects = [[NSBundle mainBundle] loadNibNamed:@"ViewOnlineMissionCell" owner:nil options:nil];
        for(id currentObject in topLevelObjects)
        {
            if([currentObject isKindOfClass:[ViewOnlineMissionCell class]])
            {
                cell = (ViewOnlineMissionCell *)currentObject;
                break;
            }
        }
    }
    if(indexSection == 0 && featured)
    {
        if(indexInt == 0)
        {
            cell.textLabel.text = @"1st Place: 25 Space Bucks";
            cell.viewButton.hidden = YES;
            cell.tweetButton.hidden = YES;
        }else if(indexInt == 1)
        {
            cell.textLabel.text = @"Top 10: 5 Space Bucks";
            cell.viewButton.hidden = YES;
            cell.tweetButton.hidden = YES;
        }
    }
    if((indexSection == 0 && !featured) || (indexSection == 1 && featured))
    {
        if(indexInt == 0)
        {
            cell.textLabel.text = [NSString stringWithFormat:@"Created By: %@",madeBy];
            cell.viewButton.hidden = YES;
            cell.tweetButton.hidden = YES;
        }else if(indexInt == 1)
        {
            cell.textLabel.text = [NSString stringWithFormat:@"Starting Fuel Available: %dkg",totalFuel];
            cell.viewButton.hidden = YES;
            cell.tweetButton.hidden = YES;
        }else if(indexInt == 2)
        {
            cell.textLabel.text = @"No one has completed this mission yet!";
            cell.viewButton.hidden = YES;
            cell.tweetButton.hidden = YES;
        }
    }else{
        
        if(yourMostFuel > 0.0 && yourFastestTime > 0.0)
        {
            if((indexSection == 1 && !featured) || (indexSection == 2 && featured))
            {
                if(indexInt == 0)
                {
                    cell.textLabel.text = [NSString stringWithFormat:@"Left Over Fuel: %.2fkg - Rank %d",yourMostFuel,yourMostFuelPlacement];
                    cell.viewButton.tag = yourMostFuelId;
                    cell.tweetButton.tag = yourMostFuelId;
                }else if(indexInt == 1)
                {
                    cell.textLabel.text = [NSString stringWithFormat:@"Fastest Time: %.2fs - Rank %d",yourFastestTime,yourFastestTimePlacement];
                    cell.viewButton.tag = yourFastestTimeId;
                    cell.tweetButton.tag = yourFastestTimeId;
                }
            }else if((indexSection == 2 && !featured) || (indexSection == 3 && featured))
            {
                NSDictionary *solutionData = [mostFuels objectAtIndex:indexInt];
                NSString *solutionString = [NSString stringWithFormat:@" %d. %.2fkg - %@",(indexInt + 1 ),[[solutionData objectForKey:@"fuel"] doubleValue],[solutionData objectForKey:@"username"]];
                if([[solutionData objectForKey:@"account_id"] intValue] == yourAccountId)
                {
                    [cell setBackgroundColor:[UIColor colorWithRed:50.0/255.0 green:79.0/255.0 blue:133.0/255.0 alpha:1]];
                    cell.textLabel.textColor = [UIColor colorWithRed:1.0 green:1.0 blue:1.0 alpha:1];
                    //solutionString = [NSString stringWithFormat:@"%@(You!)",solutionString];
                }else{
                    cell.tweetButton.hidden = YES;
                }
                cell.textLabel.text = solutionString;
                cell.viewButton.tag = [[solutionData objectForKey:@"id"] intValue];
                cell.tweetButton.tag = [[solutionData objectForKey:@"id"] intValue];
            }else if((indexSection == 3 && !featured) || (indexSection == 4 && featured))
            {
                NSDictionary *solutionData = [fastestTimes objectAtIndex:indexInt];
                NSString *solutionString = [NSString stringWithFormat:@" %d. %.2fs - %@",(indexInt + 1 ),[[solutionData objectForKey:@"time"] doubleValue],[solutionData objectForKey:@"username"]];
                if([[solutionData objectForKey:@"account_id"] intValue] == yourAccountId)
                {
                    [cell setBackgroundColor:[UIColor colorWithRed:50.0/255.0 green:79.0/255.0 blue:133.0/255.0 alpha:1]];
                    cell.textLabel.textColor = [UIColor colorWithRed:1.0 green:1.0 blue:1.0 alpha:1];
                    //solutionString = [NSString stringWithFormat:@"%@(You!)",solutionString];
                }else{
                    cell.tweetButton.hidden = YES;
                }
                cell.textLabel.text = solutionString;
                cell.viewButton.tag = [[solutionData objectForKey:@"id"] intValue];
                cell.tweetButton.tag = [[solutionData objectForKey:@"id"] intValue];
            }
        }else if(mostFuel > 0.0)
        {
            if((indexSection == 1 && !featured) || (indexSection == 2 && featured))
            {
                NSDictionary *solutionData = [mostFuels objectAtIndex:indexInt];
                NSString *solutionString = [NSString stringWithFormat:@" %d. %.2fkg - %@",(indexInt + 1 ),[[solutionData objectForKey:@"fuel"] doubleValue],[solutionData objectForKey:@"username"]];
                if([[solutionData objectForKey:@"account_id"] intValue] == yourAccountId)
                {
                    [cell setBackgroundColor:[UIColor colorWithRed:0.2 green:0.2 blue:0.5 alpha:1]];
                    //solutionString = [NSString stringWithFormat:@"%@(You!)",solutionString];
                }else{
                    cell.tweetButton.hidden = YES;
                }
                cell.textLabel.text = solutionString;
                cell.viewButton.tag = [[solutionData objectForKey:@"id"] intValue];
                cell.tweetButton.tag = [[solutionData objectForKey:@"id"] intValue];
            }else if((indexSection == 2 && !featured) || (indexSection == 3 && featured))
            {
                NSDictionary *solutionData = [fastestTimes objectAtIndex:indexInt];
                NSString *solutionString = [NSString stringWithFormat:@" %d. %.2fs - %@",(indexInt + 1 ),[[solutionData objectForKey:@"time"] doubleValue],[solutionData objectForKey:@"username"]];
                if([[solutionData objectForKey:@"account_id"] intValue] == yourAccountId)
                {
                    [cell setBackgroundColor:[UIColor colorWithRed:0.2 green:0.2 blue:0.5 alpha:1]];
                    //solutionString = [NSString stringWithFormat:@"%@(You!)",solutionString];
                }else{
                    cell.tweetButton.hidden = YES;
                }
                cell.textLabel.text = solutionString;
                cell.viewButton.tag = [[solutionData objectForKey:@"id"] intValue];
                cell.tweetButton.tag = [[solutionData objectForKey:@"id"] intValue];
            }
        }
    
    }
    
    
    [cell.viewButton addTarget:self action: @selector(viewPressed:) 
              forControlEvents:UIControlEventTouchUpInside];
    
    
    
    [cell.tweetButton addTarget:self action: @selector(tweetPressed:) 
              forControlEvents:UIControlEventTouchUpInside];
    
    cell.selectionStyle = UITableViewCellSelectionStyleNone;  
    return cell;
}

- (void)viewPressed:(UIButton *)sender
{
    if(featured)
    {
        UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"FEATURED MISSION" message:@"You cannot view any solutions until the Mission is done being featured." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
        [self.view addSubview: myAlertView];
        [myAlertView show];
        [myAlertView release];
    }else if(yourMostFuelId > 0 && yourFastestTimeId > 0)
    {
        ViewOnlineMissionHud.bottomText=@"Mission Solution";
        [ViewOnlineMissionHud setHidden:NO];
        
        [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(viewSolutionId:) userInfo:[NSNumber numberWithInt:sender.tag] repeats:NO];
    }else{
        
        UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"You haven't solved the Mission yet!" message:@"You have to solve the Mission before you can view a solution." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
        [self.view addSubview: myAlertView];
        [myAlertView show];
        [myAlertView release];
    }
}

- (void)tweetPressed:(UIButton *)sender
{
    int solutionId = sender.tag;
    //  Create an instance of the Tweet Sheet
    TWTweetComposeViewController *tweetSheet = 
    [[TWTweetComposeViewController alloc] init];
    
    // Sets the completion handler.  Note that we don't know which thread the
    // block will be called on, so we need to ensure that any UI updates occur
    // on the main queue
    tweetSheet.completionHandler = ^(TWTweetComposeViewControllerResult result) {
        switch(result) {
            case TWTweetComposeViewControllerResultCancelled:
                //  This means the user cancelled without sending the Tweet
                break;
            case TWTweetComposeViewControllerResultDone:
                [TestFlight passCheckpoint:[NSString stringWithFormat:@"Mission Success Tweet!"]];
                break;
        }
        
        
        //  dismiss the Tweet Sheet 
        dispatch_async(dispatch_get_main_queue(), ^{            
            [self dismissViewControllerAnimated:NO completion:^{
                NSLog(@"Tweet Sheet has been dismissed."); 
            }];
        });
    };
    //  Set the initial body of the Tweet
    if(mostFuelId == solutionId && fastestTimeId == solutionId)
    {
        [tweetSheet setInitialText:[NSString stringWithFormat:@"Check out my Space Flight record for fastest time and most left over fuel!\nhttp://dev.gravitationsapp.com/viewMissionSolution/%d/%d #SpaceFlight",mission_id,solutionId]];
    }else if(mostFuelId == solutionId)
    {
        [tweetSheet setInitialText:[NSString stringWithFormat:@"Check out my Space Flight record for the most left over fuel!\nhttp://dev.gravitationsapp.com/viewMissionSolution/%d/%d #SpaceFlight",mission_id,solutionId]];
    }else if(fastestTimeId == solutionId)
    {
        [tweetSheet setInitialText:[NSString stringWithFormat:@"Check out my Space Flight record for the fastest time!\nhttp://dev.gravitationsapp.com/viewMissionSolution/%d/%d #SpaceFlight",mission_id,solutionId]];
    }else{
        [tweetSheet setInitialText:[NSString stringWithFormat:@"Check out my Space Flight mission success!\nhttp://dev.gravitationsapp.com/viewMissionSolution/%d/%d #SpaceFlight",mission_id,solutionId]];
    }
    
    /*
    NSArray *filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
    NSString *fileDirectory = [NSString stringWithFormat:@"%@",[filePaths objectAtIndex:0]];
    if([[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/OnlineMission_%d.jpg", fileDirectory, mission_id]])
    {
        if (![tweetSheet addImage:[UIImage imageWithContentsOfFile:[NSString stringWithFormat:@"%@/OnlineMission_%d.jpg", fileDirectory, mission_id]]]) {
            NSLog(@"Unable to add the image!");
        }
    }
     */
    /*
    //  Add an URL to the Tweet.  You can add multiple URLs.
    if (![tweetSheet addURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/puzzles/viewMissionSolution/%d/%d",mission_id,solutionId]]]){
        NSLog(@"Unable to add the URL!");
    }
    */
    //  Presents the Tweet Sheet to the user
    [self presentViewController:tweetSheet animated:NO completion:^{
        NSLog(@"Tweet sheet has been presented.");
    }];
    [tweetSheet release];
}

- (void) viewSolutionId:(NSTimer *)timer
{
    int theID = [[timer userInfo] intValue];
    if (_viewMissionSolutionViewController == nil) {
        self.viewMissionSolutionViewController = [[[ViewMissionSolutionViewController alloc] initWithNibName:@"ViewMissionSolutionViewController" bundle:nil withMissionId:mission_id andSolutionId:theID] autorelease];
    }
    [self presentModalViewController:_viewMissionSolutionViewController animated:YES];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{
    [self setSolutionList:nil];
    [self setMissionTitle:nil];
    [self setMinusOne:nil];
    [self setPlusOne:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewDidAppear:(BOOL)animated {
    missionTitle.title = theTitle;
    if(yourVote == 1)
    {
        plusOne.style = UIBarButtonItemStyleDone;
        minusOne.style = UIBarButtonItemStyleBordered;
    }else if(yourVote == -1)
    {
        plusOne.style = UIBarButtonItemStyleBordered;
        minusOne.style = UIBarButtonItemStyleDone;
    }
    if (_viewMissionSolutionViewController != nil) {
        [_viewMissionSolutionViewController release];
        _viewMissionSolutionViewController = nil;
    }
    if (_playMissionViewController != nil) {
        [_playMissionViewController release];
        _playMissionViewController = nil;
    }
    [super viewDidAppear:animated];
}

- (void)viewWillAppear:(BOOL)animated {
    
    ViewOnlineMissionHud = [LGViewHUD defaultHUD];
    ViewOnlineMissionHud.activityIndicatorOn=YES;
    ViewOnlineMissionHud.topText=@"Loading";
    ViewOnlineMissionHud.bottomText=@"Mission Data";
    [ViewOnlineMissionHud showInView:self.view];
    [ViewOnlineMissionHud setHidden:YES];
    
    [self loadPuzzleTimes];
    [solutionList reloadData];
    
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated {
    [ViewOnlineMissionHud hideWithAnimation:HUDAnimationNone];
    [super viewWillDisappear:animated];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return UIInterfaceOrientationIsLandscape(interfaceOrientation);
}

- (IBAction)backPressed:(id)sender {
    ViewOnlineMissionHud.bottomText=@"Missions";
    [ViewOnlineMissionHud setHidden:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(goBack) userInfo:nil repeats:NO];
}

- (void)goBack {
    [self dismissModalViewControllerAnimated:YES];
}

- (IBAction)playPressed:(id)sender {
    ViewOnlineMissionHud.bottomText=@"Mission Data";
    [ViewOnlineMissionHud setHidden:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(goPlay) userInfo:nil repeats:NO];
}

- (void)goPlay {
    if (_playMissionViewController == nil) {
        self.playMissionViewController = [[[PlayMissionViewController alloc] initWithNibName:@"PlayMissionViewController" bundle:nil withMissionId:mission_id withOnline:YES] autorelease];
    }
    [self presentModalViewController:_playMissionViewController animated:YES];
}

- (IBAction)plusOnePressed:(id)sender {
    plusOne.style = UIBarButtonItemStyleDone;
    minusOne.style = UIBarButtonItemStyleBordered;
    [self submitVoteWith:1];
}

- (IBAction)minusOnePressed:(id)sender {
    plusOne.style = UIBarButtonItemStyleBordered;
    minusOne.style = UIBarButtonItemStyleDone;
    [self submitVoteWith:-1];
}

-(void)submitVoteWith:(int)theVote {
    NSString *deviceUDID = [[UIDevice currentDevice] uniqueGlobalDeviceIdentifier];
    NSURLRequest *urlRequest = [NSURLRequest requestWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/puzzles/voteForPuzzle/%@/%d/%d",deviceUDID,self.mission_id,theVote]]
                                                cachePolicy:NSURLRequestReloadIgnoringCacheData
                                            timeoutInterval:5];
    NSOperationQueue *queue = [NSOperationQueue new];
    [NSURLConnection sendAsynchronousRequest:urlRequest queue:queue completionHandler:nil];
    [queue release];
}

- (void)dealloc {
    [solutionList release];
    [missionTitle release];
    [minusOne release];
    [plusOne release];
    [onlineFileDirectory release];
    [mostFuels release];
    [fastestTimes release];
    [super dealloc];
}
@end
