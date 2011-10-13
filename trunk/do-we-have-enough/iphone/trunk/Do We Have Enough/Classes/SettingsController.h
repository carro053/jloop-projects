//
//  SettingsController.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/14/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class RootViewController;
@class LoadingView; //test

@interface SettingsController : UIViewController <UIAlertViewDelegate, NSXMLParserDelegate> {
	IBOutlet UIButton *resetButton;
	IBOutlet UIButton *closeButton;
	RootViewController *rootController;
	IBOutlet UILabel *emailAddressLabel;
	IBOutlet UISwitch *pushSwitch;
	IBOutlet UISwitch *notifyInSwitch;
	IBOutlet UISwitch *notifyOutSwitch;
	//---xml parser stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentResult;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}
@property (nonatomic, retain) UIButton *resetButton;
@property (nonatomic, retain) UIButton *closeButton;
@property (nonatomic, retain) RootViewController *rootController;
@property (nonatomic, retain) UILabel *emailAddressLabel;
@property (nonatomic, retain) UISwitch *pushSwitch;
@property (nonatomic, retain) UISwitch *notifyInSwitch;
@property (nonatomic, retain) UISwitch *notifyOutSwitch;
@property (nonatomic, retain) LoadingView *loadingView;
-(IBAction)resetButtonPressed:(id)sender;
-(IBAction)closeButtonPressed;
-(IBAction)togglePushSwitch:(id)sender;
-(IBAction)toggleInSwitch:(id)sender;
-(IBAction)toggleOutSwitch:(id)sender;
-(void)resetEmailSettings;
-(void)setStage;
- (void)saveNotifications;
- (void)saveResetEmailSettings;

@end
