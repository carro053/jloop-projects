//
//  MainMenuViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "YourMissionsViewController.h"
#import "OnlineMissionsViewController.h"
#import "FlightSchoolViewController.h"

@class Reachability;

@interface MainMenuViewController : UIViewController <UITextInputDelegate> {
    Reachability* internetReachable;
    Reachability* hostReachable;
}


- (IBAction)onlineTapped:(id)sender;
- (IBAction)yourTapped:(id)sender;
- (IBAction)schoolTapped:(id)sender;
- (IBAction)feedbackTapped:(id)sender;
@property (retain, nonatomic) IBOutlet UILabel *accountNameLabel;
@property (retain, nonatomic) IBOutlet UITextField *accountNameInput;

-(void) checkNetworkStatus:(NSNotification *)notice;

@end