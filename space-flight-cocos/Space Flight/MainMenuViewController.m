//
//  MainMenuViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "MainMenuViewController.h"
#import "LGViewHUD.h"
#import "Reachability.h"
#import "SBJson.h"
#import "UIDevice+IdentifierAddition.h"

@interface MainMenuViewController ()

@end

LGViewHUD* hud;
bool internetActive;
bool hostActive;
int account_id;
int mission_id;
int solution_id;
NSString *username;

@implementation MainMenuViewController
@synthesize accountNameLabel;
@synthesize accountNameInput;


- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        NSLog(@"Main W:%f H:%f",[[UIScreen mainScreen] bounds].size.width,[[UIScreen mainScreen] bounds].size.height);
    }
    return self;
}

-(void) checkNetworkStatus:(NSNotification *)notice
{
    // called after network status changes
    NetworkStatus internetStatus = [internetReachable currentReachabilityStatus];
    switch (internetStatus)
    {
        case NotReachable:
        {
            NSLog(@"The internet is down.");
            internetActive = NO;
            
            break;
        }
        case ReachableViaWiFi:
        {
            NSLog(@"The internet is working via WIFI.");
            internetActive = YES;
            
            break;
        }
        case ReachableViaWWAN:
        {
            NSLog(@"The internet is working via WWAN.");
            internetActive = YES;
            
            break;
        }
    }
    
    NetworkStatus hostStatus = [hostReachable currentReachabilityStatus];
    switch (hostStatus)
    {
        case NotReachable:
        {
            NSLog(@"A gateway to the host server is down.");
            hostActive = NO;
            
            break;
        }
        case ReachableViaWiFi:
        {
            NSLog(@"A gateway to the host server is working via WIFI.");
            hostActive = YES;
            
            break;
        }
        case ReachableViaWWAN:
        {
            NSLog(@"A gateway to the host server is working via WWAN.");
            hostActive = YES;
            
            break;
        }
    }
    if(internetActive)
    {
        //get account and username
        NSArray *arrayPaths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
        NSString *docDirectory = [arrayPaths objectAtIndex:0];
        NSString *filePath = [docDirectory stringByAppendingString:@"/File.txt"];
        NSString *fileContents = [NSString stringWithContentsOfFile:filePath encoding:NSUTF8StringEncoding error:nil];
        if(fileContents != nil)
        {
            accountNameInput.text = fileContents;
            username = [[NSString alloc] initWithString:fileContents];
        }
        accountNameLabel.hidden = NO;
        accountNameInput.hidden = NO;        
    }else{
        
        accountNameLabel.hidden = YES;
        accountNameInput.hidden = YES;
    }
}
- (void) viewSolution
{
    ViewMissionSolutionViewController *viewMissionSolutionViewController = [[ViewMissionSolutionViewController alloc] initWithNibName:@"ViewMissionSolutionViewController" bundle:nil withMissionId:mission_id andSolutionId:solution_id];
    [self presentModalViewController:viewMissionSolutionViewController animated:YES];
    [viewMissionSolutionViewController release];
}


- (void)viewDidLoad
{
    [super viewDidLoad];
    [TestFlight passCheckpoint:@"Main Menu"];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{
    [self setAccountNameLabel:nil];
    [self setAccountNameInput:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return UIInterfaceOrientationIsLandscape(interfaceOrientation);
}

- (void) viewWillAppear:(BOOL)animated
{
    NSLog(@"Main W:%f H:%f",[[UIScreen mainScreen] bounds].size.width,[[UIScreen mainScreen] bounds].size.height);
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(checkNetworkStatus:) name:kReachabilityChangedNotification object:nil];

    internetReachable = [[Reachability reachabilityForInternetConnection] retain];
    [internetReachable startNotifier];
    
    // check if a pathway to a random host exists
    hostReachable = [[Reachability reachabilityWithHostName: @"dev.gravitationsapp.com"] retain];
    [hostReachable startNotifier];
    
    hud = [LGViewHUD defaultHUD];
    hud.activityIndicatorOn=YES;
    hud.topText=@"Loading";
    hud.bottomText=@"Missions";
    [hud showInView:self.view];
    [hud setHidden:YES];
    
    
    [self.navigationController setNavigationBarHidden:YES animated:animated];
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated {
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [hud hideWithAnimation:HUDAnimationNone];
    [super viewWillDisappear:animated];
}

- (IBAction)onlineTapped:(id)sender {
    if(hostActive && internetActive)
    {
        [hud setHidden:NO];
        [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(loadOnline) userInfo:nil repeats:NO];
    }else{
        [self checkNetworkStatus:nil];
        UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Internet Connection Required!" message: @"You need an internet connection to view the online missions." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
        [self.view addSubview: myAlertView];
        [myAlertView show];
        [myAlertView release];
    }
}

- (void)loadOnline {
    OnlineMissionsViewController *onlineMissionsViewController = [[OnlineMissionsViewController alloc] initWithNibName:@"OnlineMissionsViewController" bundle:nil];
    [self presentModalViewController:onlineMissionsViewController animated:YES];
    [onlineMissionsViewController release];
}

- (IBAction)yourTapped:(id)sender {
    YourMissionsViewController *yourMissionsViewController = [[YourMissionsViewController alloc] initWithNibName:@"YourMissionsViewController" bundle:nil];
    [self presentModalViewController:yourMissionsViewController animated:YES];
    [yourMissionsViewController release];
}

- (IBAction)schoolTapped:(id)sender {
    FlightSchoolViewController *flightSchoolViewController = [[FlightSchoolViewController alloc] initWithNibName:@"FlightSchoolViewController" bundle:nil];
    [self presentModalViewController:flightSchoolViewController animated:YES];
    [flightSchoolViewController release];
}

- (IBAction)feedbackTapped:(id)sender {
    
    BOOL iPad = NO;
#ifdef UI_USER_INTERFACE_IDIOM
    iPad = (UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPad);
#endif
    if(!iPad)
        [UIApplication sharedApplication].statusBarOrientation = UIInterfaceOrientationPortrait;
    [TestFlight openFeedbackView];
}

- (void)touchesBegan:(NSSet *)touches withEvent:(UIEvent *)event 
{
    [accountNameInput resignFirstResponder];
}

- (void)textFieldDidEndEditing:(UITextField *)textField {
    if(![textField.text isEqualToString:@""] && ![textField.text isEqualToString:username])
    {
        NSDictionary *dict = [NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:[[UIDevice currentDevice] uniqueGlobalDeviceIdentifier],textField.text, nil] forKeys:[NSArray arrayWithObjects:@"device_id",@"username",nil]];
        SBJsonWriter *writer = [SBJsonWriter new];
        NSString *jsonData = [writer stringWithObject:dict];
        [writer release];
        NSString * post = [[NSString alloc] initWithFormat:@"json_data=%@", jsonData];
        [jsonData release];
        NSData * postData = [post dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:NO];
        NSString * postLength = [NSString stringWithFormat:@"%d",[postData length]];
        NSMutableURLRequest * request = [[[NSMutableURLRequest alloc] init] autorelease];
        [request setURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/missions/saveAccountInfo/"]]];
        [request setHTTPMethod:@"POST"];
        [request setValue:postLength forHTTPHeaderField:@"Content-Length"];
        [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
        [request setHTTPBody:postData];
        NSURLConnection * conn = [[NSURLConnection alloc] initWithRequest:request delegate:self];
        if(conn)
        {
            NSLog(@"Connection Successful");
            username = textField.text;
            NSArray *arrayPaths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
            NSString *docDirectory = [arrayPaths objectAtIndex:0];
            NSString *filePath = [docDirectory stringByAppendingString:@"/File.txt"];
            [textField.text writeToFile:filePath atomically:YES encoding:NSUTF8StringEncoding error:nil];
            UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Name Submitted" message: @"Your new account is being reviewed and will show up online within 24 hours." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
            [self.view addSubview: myAlertView];
            [myAlertView show];
            [myAlertView release];
        }
    }
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    [textField resignFirstResponder];
    return NO;
}
- (void)textFieldDidBeginEditing:(UITextField *)textField{
    
}

- (void)selectionWillChange:(UITextField *)textInput
{
    
}
- (void)selectionDidChange:(UITextField *)textInput
{
    
}
- (void)textWillChange:(UITextField *)textInput
{
    
}
- (void)textDidChange:(UITextField *)textInput
{
    
}

- (NSString *)stringWithUrl:(NSURL *)url
{
	NSURLRequest *urlRequest = [NSURLRequest requestWithURL:url
                                                cachePolicy:NSURLRequestReloadIgnoringCacheData
                                            timeoutInterval:2];
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

- (NSDictionary *) getAccountInfo
{
    NSString *deviceUDID = [[UIDevice currentDevice] uniqueGlobalDeviceIdentifier];
	id response = [self objectWithUrl:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/missions/getAccountInfo/%@",deviceUDID]]];
	NSDictionary *feed = (NSDictionary *)response;
	return feed;
}

- (void)dealloc
{
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [username release];
    [accountNameLabel release];
    [accountNameInput release];
    [super dealloc];
}

@end
