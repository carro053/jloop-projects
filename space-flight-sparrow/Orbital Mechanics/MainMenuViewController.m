//
//  MainMenuViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "MainMenuViewController.h"
#import "AppDelegate.h"
#import "LGViewHUD.h"
#import "SBJson.h"
#import "UIDevice+IdentifierAddition.h"
#import "TestFlight.h"

@interface MainMenuViewController ()

@end

LGViewHUD* hud;
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

- (void) viewSolution
{
    ViewMissionSolutionViewController *viewMissionSolutionViewController = [[ViewMissionSolutionViewController alloc] initWithNibName:@"ViewMissionSolutionViewController" bundle:nil withMissionId:mission_id andSolutionId:solution_id];
    [self presentViewController:viewMissionSolutionViewController animated:YES completion:nil];
}

- (void)viewDidAppear:(BOOL)animated
{
    
    AppDelegate *appDelegate = (AppDelegate *)[[UIApplication sharedApplication] delegate];
    if(appDelegate.internetActive)
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
    [super viewDidAppear:animated];
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
    AppDelegate *appDelegate = (AppDelegate *)[[UIApplication sharedApplication] delegate];
    if(appDelegate.hostActive && appDelegate.internetActive)
    {
        [hud setHidden:NO];
        [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(loadOnline) userInfo:nil repeats:NO];
    }else{
        UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Internet Connection Required!" message: @"You need an internet connection to view the online missions." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
        [self.view addSubview: myAlertView];
        [myAlertView show];
    }
}

- (void)loadOnline {
    OnlineMissionsViewController *onlineMissionsViewController = [[OnlineMissionsViewController alloc] initWithNibName:@"OnlineMissionsViewController" bundle:nil];
    [self presentViewController:onlineMissionsViewController animated:YES completion:nil];
}

- (IBAction)yourTapped:(id)sender {
    YourMissionsViewController *yourMissionsViewController = [[YourMissionsViewController alloc] initWithNibName:@"YourMissionsViewController" bundle:nil];
    [self presentViewController:yourMissionsViewController animated:YES completion:nil];
}

- (IBAction)schoolTapped:(id)sender {
    FlightSchoolViewController *flightSchoolViewController = [[FlightSchoolViewController alloc] initWithNibName:@"FlightSchoolViewController" bundle:nil];
    [self presentViewController:flightSchoolViewController animated:YES completion:nil];
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
        NSString * post = [[NSString alloc] initWithFormat:@"json_data=%@", jsonData];
        NSData * postData = [post dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:NO];
        NSString * postLength = [NSString stringWithFormat:@"%d",[postData length]];
        NSMutableURLRequest * request = [[NSMutableURLRequest alloc] init];
        [request setURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://gravity.jloop.com/puzzles/saveAccountInfo/"]]]; 
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
	id response = [self objectWithUrl:[NSURL URLWithString:[NSString stringWithFormat:@"http://gravity.jloop.com/puzzles/getAccountInfo/%@",deviceUDID]]];
	NSDictionary *feed = (NSDictionary *)response;
	return feed;
}

- (void)dealloc
{
    [[NSNotificationCenter defaultCenter] removeObserver:self];
}

@end
