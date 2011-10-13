//
//  InviteUserViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/23/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <AddressBook/AddressBook.h>
#import <AddressBookUI/AddressBookUI.h>
@class LoadingView;


@interface InviteUserViewController : UIViewController <ABPeoplePickerNavigationControllerDelegate, UIActionSheetDelegate, NSXMLParserDelegate> {
	IBOutlet UISwitch *groupSwitch;
	IBOutlet UITextField *emailAddress;
	NSString *event_id;
	//---xml parser stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentResult;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}
@property (nonatomic, retain) UISwitch *groupSwitch;
@property (nonatomic, retain) UITextField *emailAddress;
@property (nonatomic, retain) NSString *event_id;
@property (nonatomic, retain) LoadingView *loadingView;
-(IBAction)showPicker:(id)sender;
-(IBAction)showActionSheet:(id)sender;
-(IBAction)addEmailPressed:(id)sender;
-(IBAction)cancel:(id)sender;
-(IBAction)save:(id)sender;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
@end
