//
//  ViewMissionSolutionViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface ViewMissionSolutionViewController : UIViewController {
    int mission_id;
    int solution_id;
}
- (IBAction)backPressed:(id)sender;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId andSolutionId:(int)solutionId;

@property (retain, nonatomic) IBOutlet UIProgressView *fuelIndicator;
@property (retain, nonatomic) IBOutlet UIView *cocos2dView;

@property int mission_id;
@property int solution_id;

@end
