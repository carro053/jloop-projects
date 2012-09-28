//
//  FlightSchoolViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "FlightSchoolViewController.h"
#import "FlightSchoolCell.h"

@interface FlightSchoolViewController ()

@end

NSArray *filePaths;
NSString *fileDirectory;
NSString *fileName;
int justCompletedMission;

@implementation FlightSchoolViewController
@synthesize missionList;
@synthesize missionArray;
@synthesize completedMissions;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        missionArray = [[NSMutableArray alloc] init];
        completedMissions = [[NSMutableDictionary alloc] init];
        
        filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        fileDirectory = [[NSString alloc] initWithFormat:@"%@",[filePaths objectAtIndex:0]];
        fileName = [[NSString alloc] initWithFormat:@"%@/FlightSchool.plist", fileDirectory];
        [TestFlight passCheckpoint:@"Flight School"];
    }
    return self;
}

- (void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
    if(justCompletedMission >= 0)
    {
        int goTo = justCompletedMission + 1;
        
        NSMutableDictionary *data = [[NSMutableDictionary alloc] init];
        [data setObject:completedMissions forKey:@"completedMissions"];
        [data setObject:[NSNumber numberWithInt:-1] forKey:@"justCompletedMission"];
        if(![data writeToFile:fileName atomically:YES])
            NSLog(@"failed to write plist");
        [data release];
        if([missionArray count] <= goTo)
        {
            NSLog(@"No More Missions");
        }else{
            ViewFlightSchoolMissionViewController *viewFlightSchoolMissionViewController = [[ViewFlightSchoolMissionViewController alloc] initWithNibName:@"ViewFlightSchoolMissionViewController" bundle:nil withMissionId:goTo];    
            [self presentModalViewController:viewFlightSchoolMissionViewController animated:YES];
            [viewFlightSchoolMissionViewController release];
        }
    }
}

- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    [self loadMissions];
    [missionList reloadData];
}

- (void)loadMissions {
    BOOL fileExists = [[NSFileManager defaultManager] fileExistsAtPath:fileName];
    if(fileExists)
    {
        //read plist
        NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:fileName];
        NSArray *keys = [dict allKeys];
        //loop through keys of plist
        for(NSString *key in keys)
        {
            if([key isEqualToString:@"completedMissions"])
                completedMissions = [[NSMutableDictionary alloc] initWithDictionary:[dict objectForKey:key]];
            if([key isEqualToString:@"justCompletedMission"])
                justCompletedMission = [[dict objectForKey:key] intValue];
        }
    }else{
        completedMissions = [[NSMutableDictionary alloc] init];
        justCompletedMission = -1;
        NSMutableDictionary *data = [[NSMutableDictionary alloc] init];
        [data setObject:completedMissions forKey:@"completedMissions"];
        [data setObject:[NSNumber numberWithInt:-1] forKey:@"justCompletedMission"];
        if(![data writeToFile:fileName atomically:YES])
            NSLog(@"failed to write plist");
        [data release];
    } 
    
    [missionArray removeAllObjects];
    NSString* plistPath = [[NSBundle mainBundle] pathForResource:@"FlightSchool" ofType:@"plist"];
    NSDictionary *dict = [NSDictionary dictionaryWithContentsOfFile:plistPath];
    for(NSDictionary *mission in [dict objectForKey:@"flightSchoolArray"])
    {
        [missionArray addObject:mission];
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
    int indexInt = indexPath.row;
    
    NSDictionary *dict = [NSDictionary dictionaryWithDictionary:[missionArray objectAtIndex:indexInt]];
    static NSString *CellIdentifier = @"FlightSchoolCell";
    
    FlightSchoolCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *topLevelObjects = [[NSBundle mainBundle] loadNibNamed:@"FlightSchoolCell" owner:nil options:nil];
        for(id currentObject in topLevelObjects)
        {
            if([currentObject isKindOfClass:[FlightSchoolCell class]])
            {
                cell = (FlightSchoolCell *)currentObject;
                break;
            }
        }
    }
    
    NSString* imagePath = [[NSBundle mainBundle] pathForResource:[NSString stringWithFormat:@"FlightSchoolMission%d",indexInt] ofType:@"jpg"];
    cell.imageView.image = [UIImage imageWithContentsOfFile:imagePath];
    cell.title.text = [dict objectForKey:@"name"];
    if([[completedMissions objectForKey:[NSString stringWithFormat:@"%d",indexInt]] boolValue])
    {
        cell.completed.hidden = NO;
    }else{
        cell.completed.hidden = YES;
    }
    
    cell.viewButton.tag = indexInt;
    
    [cell.viewButton addTarget:self action: @selector(viewPressed:) 
              forControlEvents:UIControlEventTouchUpInside];
    
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    return cell;
}

- (IBAction)viewPressed:(id)sender {
    UIButton *senderButton = (UIButton *)sender;
    int missionId = senderButton.tag;
    ViewFlightSchoolMissionViewController *viewFlightSchoolMissionViewController = [[ViewFlightSchoolMissionViewController alloc] initWithNibName:@"ViewFlightSchoolMissionViewController" bundle:nil withMissionId:missionId];    
    [self presentModalViewController:viewFlightSchoolMissionViewController animated:YES];
    [viewFlightSchoolMissionViewController release];
}

- (IBAction)backPressed:(id)sender {
	[self dismissModalViewControllerAnimated:YES];    
}
- (void)dealloc {
    [fileDirectory release];
    [fileName release];
    [missionArray release];
    [completedMissions release];
    [missionList release];
    [super dealloc];
}
@end
