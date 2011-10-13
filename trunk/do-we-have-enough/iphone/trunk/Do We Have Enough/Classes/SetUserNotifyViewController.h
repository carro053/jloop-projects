//
//  SetUserNotifyViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/24/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class EventDetailsViewController;
@class LoadingView;

@interface SetUserNotifyViewController : UIViewController <NSXMLParserDelegate> {
	IBOutlet UIPickerView *notifyPicker;
	int myNotify;
	EventDetailsViewController *parentController;
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
@property (nonatomic, retain) UIPickerView *notifyPicker;
@property (nonatomic, readwrite) int myNotify;
@property (nonatomic, retain) EventDetailsViewController *parentController;
@property (nonatomic, retain) LoadingView *loadingView;
@property (nonatomic, retain) NSString *event_id;
-(IBAction)cancel:(id)sender;
-(IBAction)save:(id)sender;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
@end
