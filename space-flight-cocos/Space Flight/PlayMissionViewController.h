//
//  PlayMissionViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "CorePlot-CocoaTouch.h"
#import "FuelUsedScatterPlot.h"


@interface PlayMissionViewController : UIViewController {
    int mission_id;
    BOOL online;
    BOOL play;
    BOOL graphIt;
    BOOL gravity;
    IBOutlet CPTGraphHostingView *_graphHostingView;
    FuelUsedScatterPlot *_scatterPlot;
}
- (IBAction)goPressed:(id)sender;
- (IBAction)graphPressed:(id)sender;
- (IBAction)backPressed:(id)sender;
- (IBAction)gravityPressed:(id)sender;
@property (retain, nonatomic) IBOutlet UIProgressView *fuelIndicator;
@property (retain, nonatomic) IBOutlet UIView *cocos2dView;
@property (retain, nonatomic) IBOutlet UIBarButtonItem *fidoButton;
@property (nonatomic, retain) FuelUsedScatterPlot *scatterPlot;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId withOnline:(BOOL)isOnline;


- (void)graphThis:(NSMutableArray *)shipPaths;


@property int mission_id;
@property BOOL online;
@property BOOL play;
@property BOOL graphIt;
@property BOOL gravity;

@end
