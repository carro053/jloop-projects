//
//  YourMissionsViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "YourMissionsViewController.h"
#import "AppDelegate.h"
#import "OnlineMissionsViewController.h"
#import "YourMissionCell.h"
#import "EPUploader.h"
#import "UIDevice+IdentifierAddition.h"
#import "SBJson.h"
#import "LGViewHUD.h"
#import "TestFlight.h"

@interface YourMissionsViewController ()

@end

NSArray *filePaths;
NSString *fileDirectory;
NSString *fileName;

NSMutableArray *missionArray;
int lastMissionId;
int submitMissionId;

LGViewHUD* YourMissionsHud;

@implementation YourMissionsViewController
@synthesize editMissionViewController = _editMissionViewController;
@synthesize playMissionViewController = _playMissionViewController;
@synthesize missionList = _missionList;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        fileDirectory = [[NSString alloc] initWithFormat:@"%@",[filePaths objectAtIndex:0]];
        fileName = [[NSString alloc] initWithFormat:@"%@/CustomMissions.plist", fileDirectory];
        [TestFlight passCheckpoint:@"Your Missions"];
        [self getMissions];
    }
    return self;
}

- (void) getMissions {
    //declare vars to be found in plist
    //check if plist exists
    BOOL fileExists = [[NSFileManager defaultManager] fileExistsAtPath:fileName];
    if(fileExists)
    {
        //read plist
        NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:fileName];
        NSArray *keys = [dict allKeys];
        //loop through keys of plist
        for(NSString *key in keys)
        {
            if([key isEqualToString:@"missionArray"])
                missionArray = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"lastMissionId"])
                lastMissionId = [[dict objectForKey:key] intValue];
        }
    }else{
        missionArray = [[NSMutableArray alloc] init];
        NSMutableDictionary *data = [[NSMutableDictionary alloc] init];
        [data setObject:missionArray forKey:@"missionArray"];
        [data setObject:[NSNumber numberWithInt:lastMissionId] forKey:@"lastMissionId"];
        [data writeToFile:fileName atomically:YES];
    } 
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{
    [self setMissionList:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return UIInterfaceOrientationIsLandscape(interfaceOrientation);
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return [missionArray count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    int indexInt = [missionArray count] - indexPath.row - 1;
    int missionId = [[missionArray objectAtIndex:indexInt] intValue];
    
    NSString *playFileName = [NSString stringWithFormat:@"%@/CustomMission_%d.plist", fileDirectory, missionId];
    //read plist
    NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:playFileName];
    int serverId = [[dict objectForKey:@"server_id"] intValue];
    bool solved = [[dict objectForKey:@"solved"] boolValue];
    NSString *title = [dict objectForKey:@"name"];
    static NSString *CellIdentifier = @"YourMissionCell";
    
    YourMissionCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *topLevelObjects = [[NSBundle mainBundle] loadNibNamed:@"YourMissionCell" owner:nil options:nil];
        for(id currentObject in topLevelObjects)
        {
            if([currentObject isKindOfClass:[YourMissionCell class]])
            {
                cell = (YourMissionCell *)currentObject;
                break;
            }
        }
    }
    cell.imageView.image = [UIImage imageWithContentsOfFile:[NSString stringWithFormat:@"%@/CustomMission_%d.jpg", fileDirectory,missionId]];
    cell.title.text = title;
    if(serverId == 0 && solved)
    {
        cell.submitButton.hidden = NO;
        cell.submitted.hidden = YES;
    }else if(serverId > 0)
    {
        cell.submitButton.hidden = YES;
        cell.submitted.hidden = NO;
        cell.submitted.text = [NSString stringWithFormat:@"Submitted: %@",[dict objectForKey:@"submitted"]];
    }else{
        cell.submitButton.hidden = YES;
        cell.submitted.hidden = NO;
        cell.submitted.text = @"Solve to Unlock Submit";
    }
    
    cell.playButton.tag = missionId;
    cell.editButton.tag = missionId;
    cell.submitButton.tag = missionId;
    
    [cell.playButton addTarget:self action: @selector(playPressed:) 
              forControlEvents:UIControlEventTouchUpInside];
    [cell.editButton addTarget:self action: @selector(editPressed:) 
              forControlEvents:UIControlEventTouchUpInside];
    [cell.submitButton addTarget:self action: @selector(submitPressed:) 
              forControlEvents:UIControlEventTouchUpInside];
    
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    return cell;
}

- (IBAction)editPressed:(id)sender {
    UIButton *senderButton = (UIButton *)sender;
    int missionId = senderButton.tag;
    YourMissionsHud.topText = @"Loading";
    YourMissionsHud.bottomText = @"Mission";
    [YourMissionsHud setHidden:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(editMissionId:) userInfo:[NSNumber numberWithInt:missionId] repeats:NO];
}

- (void) editMissionId:(NSTimer *)timer
{
    int theID = [[timer userInfo] intValue];
    if (_editMissionViewController == nil) {
        self.editMissionViewController = [[EditMissionViewController alloc] initWithNibName:@"EditMissionViewController" bundle:nil withMissionId:theID];
    }
    [self presentViewController:_editMissionViewController animated:YES completion:nil];
}

- (IBAction)playPressed:(id)sender {
    UIButton *senderButton = (UIButton *)sender;
    int missionId = senderButton.tag;
    YourMissionsHud.topText = @"Loading";
    YourMissionsHud.bottomText = @"Mission";
    [YourMissionsHud setHidden:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(playMissionId:) userInfo:[NSNumber numberWithInt:missionId] repeats:NO];
}

- (void)playMissionId:(NSTimer *)timer
{
    int theID = [[timer userInfo] intValue];
    if (_playMissionViewController == nil) {
        self.playMissionViewController = [[PlayMissionViewController alloc] initWithNibName:@"PlayMissionViewController" bundle:nil withMissionId:theID withOnline:NO];
    }
    [self presentViewController:_playMissionViewController animated:YES completion:nil];
}

- (IBAction)submitPressed:(id)sender {
    
    AppDelegate *appDelegate = (AppDelegate *)[[UIApplication sharedApplication] delegate];
    if(appDelegate.hostActive && appDelegate.internetActive)
    {
        UIButton *senderButton = (UIButton *)sender;
        int missionId = senderButton.tag;
        submitMissionId = missionId;
        [self submitMission];
    }else{
        UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Internet Connection Required!" message: @"You need an internet connection to submit your mission." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
        [self.view addSubview: myAlertView];
        [myAlertView show];
    }
}


-(void) submitMission {
    UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Submit Your Mission!" message:@"You can submit your mission for everyone else to play. You can only submit each mission once so make sure it is ready when you submit it." delegate: self cancelButtonTitle: @"Cancel" otherButtonTitles:@"Submit!", nil];
    [self.view addSubview: myAlertView];
    [myAlertView show];
}

- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex
{
    if(buttonIndex == 1)
    {
        YourMissionsHud.topText = @"Submitting";
        YourMissionsHud.bottomText = @"Mission";
        [YourMissionsHud setHidden:NO];
        NSString *playFileName = [NSString stringWithFormat:@"%@/CustomMission_%d.plist", fileDirectory, submitMissionId];
        //read plist
        NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:playFileName];
        NSString *deviceUDID = [[UIDevice currentDevice] uniqueGlobalDeviceIdentifier];
        [dict setObject:deviceUDID forKey:@"device_id"];
        SBJsonWriter *writer = [SBJsonWriter new];
        NSString *jsonData = [writer stringWithObject:dict];
        NSString * post = [[NSString alloc] initWithFormat:@"json_data=%@", jsonData];
        NSData * postData = [post dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:NO];
        NSString * postLength = [NSString stringWithFormat:@"%d",[postData length]];
        NSMutableURLRequest * request = [[NSMutableURLRequest alloc] init];
        [request setURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://gravity.jloop.com/puzzles/savePuzzle/35"]]]; 
        [request setHTTPMethod:@"POST"];
        [request setValue:postLength forHTTPHeaderField:@"Content-Length"];
        [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
        [request setHTTPBody:postData];
        NSURLConnection * conn = [[NSURLConnection alloc] initWithRequest:request delegate:self];
        if(conn)
        {
            NSLog(@"Connection Successful");
        }
    }
}

- (void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data {
    NSString* returnString= [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
    int returnCode = [returnString intValue];
    NSString *playFileName = [NSString stringWithFormat:@"%@/CustomMission_%d.plist", fileDirectory, submitMissionId];
    NSMutableDictionary *missionsdict = [NSMutableDictionary dictionaryWithContentsOfFile:playFileName];
    [missionsdict setObject:[NSNumber numberWithInt:returnCode] forKey:@"server_id"];
    NSDateFormatter *formatter;
    NSString        *dateString;
    
    formatter = [[NSDateFormatter alloc] init];
    [formatter setDateFormat:@"MM/dd/yyyy"];
    
    dateString = [formatter stringFromDate:[NSDate date]];
    
    [missionsdict setObject:dateString forKey:@"submitted"];
    if(![missionsdict writeToFile:playFileName atomically:NO])
        NSLog(@"failed to write plist");
    if(![[EPUploader alloc] initWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://gravity.jloop.com/puzzles/saveImage/%d/1",returnCode]] filePath:[NSString stringWithFormat:@"%@/CustomMission_%d@2x.jpg", fileDirectory,submitMissionId] delegate:self doneSelector:@selector(onUploadDone:) errorSelector:@selector(onUploadError:)])
        NSLog(@"Failed Upload");
    
    [YourMissionsHud setHidden:YES];
    [self.missionList reloadData];
    
    UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Mission Submitted!" message: @"Your mission has successfully been submitted." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
    [self.view addSubview: myAlertView];
    [myAlertView show];
}
- (void) onUploadDone:(id)sender {
    NSLog(@"DONE");
}
- (void) onUploadError:(id)sender {
    NSLog(@"ERROR");
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    /*
    int indexInt = indexPath.row;
    int missionId = [[[missionArray objectAtIndex:indexInt] objectAtIndex:1] intValue];
    [self editMissionId:missionId];
    */
}

- (NSString *)randomLetter {
    NSString *letters = @"ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    return [letters substringWithRange:[letters rangeOfComposedCharacterSequenceAtIndex:arc4random()%[letters length]]];
}
- (NSString *)randomLetterOrNumber {
    NSString *letters = @"ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return [letters substringWithRange:[letters rangeOfComposedCharacterSequenceAtIndex:arc4random()%[letters length]]];
}
- (NSString *)randomNumber {
    NSString *letters = @"0123456789";
    return [letters substringWithRange:[letters rangeOfComposedCharacterSequenceAtIndex:arc4random()%[letters length]]];
}

- (void) newMission
{
    lastMissionId++;
    NSString *path = [NSString stringWithFormat:@"%@/CustomMission_%d.plist", fileDirectory,lastMissionId];
    NSMutableDictionary *data = [[NSMutableDictionary alloc] init];
    NSArray *astronauts = [NSArray arrayWithObjects:[NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:[NSNumber numberWithDouble:512.0],[NSNumber numberWithDouble:360.0], nil] forKeys:[NSArray arrayWithObjects:@"x",@"y",nil]],nil];
    NSArray *planets = [NSArray array];
    double total_fuel = 400.0;
    NSArray *start = [NSArray arrayWithObjects:[NSNumber numberWithDouble:240.0],[NSNumber numberWithDouble:160.0],nil];
    NSArray *end = [NSArray arrayWithObjects:[NSNumber numberWithDouble:784.0],[NSNumber numberWithDouble:558.0],nil];
    
    NSArray *names = [NSMutableArray arrayWithObjects:@"Cygnus",@"Londinium",@"Whittier",@"Pelorum",@"Regina",@"Sihnon",@"Ariel",@"Beaumonde",@"Newhall",@"New Melbourne",@"Bernadette",@"Angel",@"Deadwood",@"New Melbourne",@"Regina",@"Ares",@"Ariel",@"Londinium",@"Shadow",@"Lilac",@"Triumph",@"Beylix",@"Boros",@"Bernadette",@"Parth",@"Newhall",@"Regina",@"Londinium",@"Pelorum",@"Sihnon",@"Triumph",@"Newhall",@"Osiris",@"Higgins' Moon",@"Sihnon",@"New Melbourne",@"Liann Jiun",@"Lazarus",@"Londinium",@"Persephone",@"Whittier",@"Ares",@"Ita",@"Bernadette",@"Kerry",@"Perth",@"Lazarus",@"Highgate",@"Ariel",@"Paquin",@"Athens",@"Zhone",@"Cardi",@"Kora",@"Ussos",@"Mandia",@"Horia",@"Calda",@"Pola",@"Tara",@"Alan",@"Osiris",@"St. Albans",@"Zephyr",@"Sihnon",@"Newhall",@"Hera",@"Bernadette",@"Lazarus",@"Liann",@"Jiun",@"Myr",@"Stevens",@"Louis",@"Malcom",@"Coheed",@"Cambria",@"Pastell",@"Stratford",@"Le",@"Tes",@"Ryan",@"Insectoid",@"Picard",@"Voltaic",@"Girard",@"Raspberry Pi", nil];
    
    
    NSString *name = [NSString stringWithFormat:@"%@ %@-%@",[names objectAtIndex:arc4random() % [names count]],[self randomLetter],[self randomNumber]];
    
    [data setObject:name forKey:@"name"];
    [data setObject:astronauts forKey:@"astronauts"];
    [data setObject:planets forKey:@"planets"];
    [data setObject:planets forKey:@"items"];
    [data setObject:planets forKey:@"wells"];
    [data setObject:[NSNumber numberWithDouble:total_fuel] forKey:@"total_fuel"];
    [data setObject:start forKey:@"startPoint"];
    [data setObject:end forKey:@"endPoint"];
    [data setObject:[NSNumber numberWithBool:YES] forKey:@"isNew"];
    if([data writeToFile:path atomically:YES])
    {
        NSMutableDictionary *missionsdict = [NSMutableDictionary dictionaryWithContentsOfFile:fileName];
        [missionArray addObject:[NSNumber numberWithInt:lastMissionId]];
        [missionsdict setObject:missionArray forKey:@"missionArray"];
        [missionsdict setObject:[NSNumber numberWithInt:lastMissionId] forKey:@"lastMissionId"];
        if(![missionsdict writeToFile:fileName atomically:NO])
            NSLog(@"failed to write plist");
    }
    YourMissionsHud.topText = @"Creating";
    YourMissionsHud.bottomText = @"Mission";
    [YourMissionsHud setHidden:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(editMissionId:) userInfo:[NSNumber numberWithInt:lastMissionId] repeats:NO];
}


- (IBAction)newMissionPressed:(id)sender {
    [self newMission];
}

- (IBAction)backPressed:(id)sender {
	[self dismissViewControllerAnimated:YES completion:nil];
}

- (void) viewWillAppear:(BOOL)animated
{
    YourMissionsHud = [LGViewHUD defaultHUD];
    YourMissionsHud.activityIndicatorOn=YES;
    YourMissionsHud.topText=@"Submitting";
    YourMissionsHud.bottomText=@"Mission";
    [YourMissionsHud showInView:self.view];
    [YourMissionsHud setHidden:YES];
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated {
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [YourMissionsHud hideWithAnimation:HUDAnimationNone];
    [super viewWillDisappear:animated];
}

- (void)viewDidAppear:(BOOL)animated {
    if (_editMissionViewController != nil) {
        _editMissionViewController = nil;
    }
    if (_playMissionViewController != nil) {
        _playMissionViewController = nil;
    }
    [self getMissions];
    [self.missionList reloadData];
}

@end
