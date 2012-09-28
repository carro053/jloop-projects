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

@interface MainMenuViewController : UIViewController <UITextInputDelegate> {
}


- (IBAction)onlineTapped:(id)sender;
- (IBAction)yourTapped:(id)sender;
- (IBAction)schoolTapped:(id)sender;
- (IBAction)feedbackTapped:(id)sender;
@property (strong, nonatomic) IBOutlet UILabel *accountNameLabel;
@property (strong, nonatomic) IBOutlet UITextField *accountNameInput;


@end