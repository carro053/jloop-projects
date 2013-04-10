//
//  OnlineMissionsViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "OnlineMissionsViewController.h"
#import "SBJson.h"
#import "LGViewHUD.h"
#import "OnlineMissionCell.h"

@interface OnlineMissionsViewController ()

@end

NSMutableData *requestData;
LGViewHUD* OnlineMissionsHud;
bool orderByRating;
int missionOffset;
int missionsPerPage;
int totalMissions;
bool loadingMoreMissions;

@implementation OnlineMissionsViewController
@synthesize orderButton;
@synthesize missionList;
@synthesize missionArray;
@synthesize imageDownloadsInProgress;


- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        orderByRating = YES;
        missionOffset = 0;
        missionsPerPage = 20;
#ifdef UI_USER_INTERFACE_IDIOM
        if(UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPad)
            missionsPerPage = 36;
#endif
        missionArray = [[NSMutableArray alloc] init];
        [TestFlight passCheckpoint:@"Online Missions"];
    }
    return self;
}

- (void)startIconDownload:(int)missionId forIndexPath:(NSIndexPath *)indexPath
{
    IconDownloader *iconDownloader = [imageDownloadsInProgress objectForKey:indexPath];
    if (iconDownloader == nil) 
    {
        iconDownloader = [[IconDownloader alloc] init];
        iconDownloader.missionId = missionId;
        iconDownloader.indexPathInTableView = indexPath;
        iconDownloader.delegate = self;
        [imageDownloadsInProgress setObject:iconDownloader forKey:indexPath];
        [iconDownloader startDownload];
        [iconDownloader release];   
    }
}

// this method is used in case the user scrolled into a set of cells that don't have their app icons yet
- (void)loadImagesForOnscreenRows
{
    if ([self.missionArray count] > 0)
    {
        NSArray *visiblePaths = [self.missionList indexPathsForVisibleRows];
        for (NSIndexPath *indexPath in visiblePaths)
        {
            if(indexPath.row < [missionArray count])
            {
                NSDictionary *mission = [self.missionArray objectAtIndex:indexPath.row];
                
                NSArray *filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
                NSString *fileDirectory = [NSString stringWithFormat:@"%@",[filePaths objectAtIndex:0]];
                if(![[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/OnlineMission_%d.jpg", fileDirectory, [[mission objectForKey:@"id"] intValue]]])
                {
                    [self startIconDownload:[[mission objectForKey:@"id"] intValue] forIndexPath:indexPath];
                }
            }
        }
    }
}

- (void)appImageDidLoad:(NSIndexPath *)indexPath
{
    IconDownloader *iconDownloader = [imageDownloadsInProgress objectForKey:indexPath];
    if (iconDownloader != nil)
    {
        UITableViewCell *cell = [self.missionList cellForRowAtIndexPath:iconDownloader.indexPathInTableView];
        
        NSArray *filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        NSString *fileDirectory = [NSString stringWithFormat:@"%@",[filePaths objectAtIndex:0]];
        
        cell.imageView.image = [UIImage imageWithContentsOfFile:[NSString stringWithFormat:@"%@/OnlineMission_%d.jpg", fileDirectory, iconDownloader.missionId]];
    }
}

- (void)getMissions {
    [missionArray removeAllObjects];
    NSMutableArray *tempMissionArray = [[NSMutableArray alloc] initWithArray:[self getOnlineMissions]];
    for (NSDictionary *tempMission in tempMissionArray)
    {
        [missionArray addObject:tempMission];
    }
    [tempMissionArray release];
    if([missionArray count] == 0)
        [self getMissions];
}
- (void)loadMoreMissions {
    NSMutableArray *tempMissionArray = [[NSMutableArray alloc] initWithArray:[self getOnlineMissions]];
    for (NSDictionary *tempMission in tempMissionArray)
    {
        [missionArray addObject:tempMission];
    }
    [tempMissionArray release];
    [self.missionList reloadData];
    loadingMoreMissions = NO;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    if([missionArray count] != totalMissions)
       return [missionArray count] + 1;
    return totalMissions;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    int indexInt = indexPath.row;
    if(indexPath.row == [missionArray count])
    {
        UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"LoadingOnlineMissionCell"];
        if (cell == nil) {
            NSArray *topLevelObjects = [[NSBundle mainBundle] loadNibNamed:@"LoadingOnlineMissionCell" owner:self options:nil];
            // Grab a pointer to the first object (presumably the custom cell, as that's all the XIB should contain).
            cell = [topLevelObjects objectAtIndex:0];
        }
        return cell;
        /*static NSString *CellIdentifier = @"OnlineMissionCell";
        
        OnlineMissionCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
        if (cell == nil) {
            NSArray *topLevelObjects = [[NSBundle mainBundle] loadNibNamed:@"OnlineMissionCell" owner:nil options:nil];
            for(id currentObject in topLevelObjects)
            {
                if([currentObject isKindOfClass:[OnlineMissionCell class]])
                {
                    cell = (OnlineMissionCell *)currentObject;
                    break;
                }
            }
        }
        cell.title.text = @"";
        cell.rating.text = @"";
        cell.submitted.text = @"";
        cell.viewButton.tag = 0;
        cell.viewButton.hidden = YES;
        cell.imageView.image = [UIImage imageNamed:@"loading_image.jpg"];
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        return cell;
        */
    }
    NSDictionary *mission = [missionArray objectAtIndex:indexInt];
    int missionId = [[mission objectForKey:@"id"] intValue];
    static NSString *CellIdentifier = @"OnlineMissionCell";
    
    OnlineMissionCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *topLevelObjects = [[NSBundle mainBundle] loadNibNamed:@"OnlineMissionCell" owner:nil options:nil];
        for(id currentObject in topLevelObjects)
        {
            if([currentObject isKindOfClass:[OnlineMissionCell class]])
            {
                cell = (OnlineMissionCell *)currentObject;
                break;
            }
        }
    }
    
    NSArray *filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
    NSString *fileDirectory = [NSString stringWithFormat:@"%@",[filePaths objectAtIndex:0]];
    if(![[NSFileManager defaultManager] fileExistsAtPath:[NSString stringWithFormat:@"%@/OnlineMission_%d.jpg", fileDirectory, missionId]])
    {
        cell.imageView.image = [UIImage imageNamed:@"loading_image.jpg"];
        if (self.missionList.dragging == NO && self.missionList.decelerating == NO)
        {
            [self startIconDownload:missionId forIndexPath:indexPath];
        }
    }else{
        cell.imageView.image = [UIImage imageWithContentsOfFile:[NSString stringWithFormat:@"%@/OnlineMission_%d.jpg", fileDirectory, missionId]];
    }
    cell.title.text = [NSString stringWithFormat:@"%@",[mission objectForKey:@"title"]];
    
    cell.rating.text = [NSString stringWithFormat:@"By %@",[mission objectForKey:@"account_username"]];
    
    if([[mission objectForKey:@"featured"] boolValue])
    {
        [cell.contentView setBackgroundColor:[UIColor colorWithRed:50.0/255.0 green:79.0/255.0 blue:133.0/255.0 alpha:1]];
        cell.title.textColor = [UIColor colorWithRed:1.0 green:1.0 blue:1.0 alpha:1];
        cell.rating.textColor = [UIColor colorWithRed:1.0 green:1.0 blue:1.0 alpha:1];
        cell.submitted.textColor = [UIColor colorWithRed:1.0 green:1.0 blue:1.0 alpha:1];
        cell.submitted.text = @"FEATURED";
    }else{
        [cell.contentView setBackgroundColor:[UIColor colorWithRed:255.0/255.0 green:255.0/255.0 blue:255.0/255.0 alpha:1]];
        cell.title.textColor = [UIColor colorWithRed:0.0 green:0.0 blue:0.0 alpha:1];
        cell.rating.textColor = [UIColor colorWithRed:0.0 green:0.0 blue:0.0 alpha:1];
        cell.submitted.textColor = [UIColor colorWithRed:0.0 green:0.0 blue:0.0 alpha:1];
        NSDateFormatter *formatter = [[NSDateFormatter alloc] init];
        [formatter setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
        NSDate *date = [formatter dateFromString:[mission objectForKey:@"created"]];
        [formatter release];
        NSDateFormatter *outputformatter = [[NSDateFormatter alloc] init];
        [outputformatter setDateFormat:@"MM/dd/yyyy"];
        cell.submitted.text = [outputformatter stringFromDate:date];
        [outputformatter release];
    }
    
    cell.viewButton.tag = missionId;
    
    [cell.viewButton addTarget:self action: @selector(viewPressed:) 
              forControlEvents:UIControlEventTouchUpInside];

    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    return cell;
}
- (void)tableView:(UITableView *)tableView willDisplayCell:(UITableViewCell *)cell    forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (!loadingMoreMissions && indexPath.row == [missionArray count]) {
        loadingMoreMissions = YES;
        missionOffset += missionsPerPage;
        //[self loadMoreMissions];
        [NSTimer scheduledTimerWithTimeInterval:0.001 target:self selector:@selector(loadMoreMissions) userInfo:nil repeats:NO];
    }
}

- (void)scrollViewDidEndDragging:(UIScrollView *)scrollView willDecelerate:(BOOL)decelerate {
    
    if (!decelerate)
	{
        [self loadImagesForOnscreenRows];
    }
}

- (void)scrollViewDidEndDecelerating:(UIScrollView *)scrollView
{
    [self loadImagesForOnscreenRows];
}

- (IBAction)viewPressed:(id)sender {
    UIButton *senderButton = (UIButton *)sender;
    int missionId = senderButton.tag;
    
    OnlineMissionsHud.topText=@"Loading";
    OnlineMissionsHud.bottomText=@"Mission Data";
    [OnlineMissionsHud setHidden:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(viewMissionId:) userInfo:[NSNumber numberWithInt:missionId] repeats:NO];
}

- (void) viewMissionId:(NSTimer *)timer
{
    int theID = [[timer userInfo] intValue];
    ViewOnlineMissionViewController *viewOnlineMissionViewController = [[ViewOnlineMissionViewController alloc] initWithNibName:@"ViewOnlineMissionViewController" bundle:nil withMissionId:theID];    
    [self presentModalViewController:viewOnlineMissionViewController animated:YES];
    [viewOnlineMissionViewController release];
}


- (IBAction)orderPressed:(id)sender {
    
    OnlineMissionsHud.topText=@"Loading";
    OnlineMissionsHud.bottomText=@"Missions";
    [OnlineMissionsHud setHidden:NO];
    if(orderByRating)
    {
        self.orderButton.title = @"Order By Rating";
        orderByRating = NO;
    }else{
        self.orderButton.title = @"Order By Newest";
        orderByRating = YES;
    }
    missionOffset = 0;
    [self.missionList scrollRectToVisible:CGRectMake(0, 0, 1, 1) animated:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(loadNewOrder) userInfo:nil repeats:NO];
}

- (void) loadNewOrder {
    [self getMissions];
    [self.missionList reloadData];
    [OnlineMissionsHud setHidden:YES];
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
- (int) getTotalMissions 
{
	id response = [self objectWithUrl:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/missions/getTotalMissions/35"]]];
	NSArray *feed = (NSArray *)response;
    int total = [[feed objectAtIndex:0] intValue];
	return total;
}

- (NSArray *) getOnlineMissions 
{
	id response = [self objectWithUrl:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/missions/getMissions/%d/%d/%d/35",orderByRating,missionOffset,missionsPerPage]]];
	NSArray *feed = (NSArray *)response;
	return feed;
}

- (void)viewWillAppear:(BOOL)animated {
    
    OnlineMissionsHud = [LGViewHUD defaultHUD];
    OnlineMissionsHud.activityIndicatorOn=YES;
    OnlineMissionsHud.topText=@"Loading";
    OnlineMissionsHud.bottomText=@"Mission Data";
    [OnlineMissionsHud showInView:self.view];
    [OnlineMissionsHud setHidden:YES];
    totalMissions = [self getTotalMissions];
    missionOffset = 0;
    int tempMissionsPerPage = missionsPerPage;
    if([missionArray count] > missionsPerPage)
        missionsPerPage = [missionArray count];
    [self getMissions];
    missionOffset = missionsPerPage - tempMissionsPerPage;
    missionsPerPage = tempMissionsPerPage;
    self.orderButton.title = @"Order By Newest";
    orderByRating = YES;
    [self.missionList reloadData];
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated {
    [OnlineMissionsHud hideWithAnimation:HUDAnimationNone];
    [super viewWillDisappear:animated];
}

- (void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    self.imageDownloadsInProgress = [NSMutableDictionary dictionary];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{
    [self setMissionList:nil];
    [self setOrderButton:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return UIInterfaceOrientationIsLandscape(interfaceOrientation);
}

- (IBAction)backPressed:(id)sender {
	[self dismissModalViewControllerAnimated:YES];    
}

- (void)dealloc {
    [missionArray release];
    [missionList release];
	[imageDownloadsInProgress release];
    [orderButton release];
    [super dealloc];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    
    // terminate all pending download connections
    NSArray *allDownloads = [self.imageDownloadsInProgress allValues];
    [allDownloads makeObjectsPerformSelector:@selector(cancelDownload)];
}

@end
