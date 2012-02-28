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

@interface SettingsController : UIViewController <UIAlertViewDelegate, NSXMLParserDelegate, UITextFieldDelegate> {
	IBOutlet UIButton *resetButton;
	IBOutlet UIButton *closeButton;
	RootViewController *rootController;
	IBOutlet UILabel *emailAddressLabel;
	IBOutlet UISwitch *notifyInSwitch;
	IBOutlet UISwitch *notifyOutSwitch;
	IBOutlet UISwitch *notifyEventChangeSwitch;
	IBOutlet UISwitch *appNotifyInSwitch;
	IBOutlet UISwitch *appNotifyOutSwitch;
	IBOutlet UISwitch *appNotifyEventChangeSwitch;
    IBOutlet UIScrollView *scroller;
    IBOutlet UITextField *userNameField;
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
@property (nonatomic, retain) UISwitch *notifyInSwitch;
@property (nonatomic, retain) UISwitch *notifyOutSwitch;
@property (nonatomic, retain) UISwitch *notifyEventChangeSwitch;
@property (nonatomic, retain) UISwitch *appNotifyInSwitch;
@property (nonatomic, retain) UISwitch *appNotifyOutSwitch;
@property (nonatomic, retain) UISwitch *appNotifyEventChangeSwitch;
@property (nonatomic, retain) UIScrollView *scroller;
@property (weak, nonatomic) IBOutlet UITextField *userNameField;

@property (nonatomic, retain) LoadingView *loadingView;
-(IBAction)resetButtonPressed:(id)sender;
-(IBAction)closeButtonPressed;
-(IBAction)toggleInSwitch:(id)sender;
-(IBAction)toggleOutSwitch:(id)sender;
-(IBAction)toggleEventChangeSwitch:(id)sender;
-(IBAction)toggleAppInSwitch:(id)sender;
-(IBAction)toggleAppOutSwitch:(id)sender;
-(IBAction)toggleAppEventChangeSwitch:(id)sender;
-(IBAction)dismissKeyboard:(id)sender;
-(void)saveName:(NSString *)theName;
-(void)resetEmailSettings;
-(void)setStage;
- (void)saveNotifications;
- (void)saveResetEmailSettings;

@end
