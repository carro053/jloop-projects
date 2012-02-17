//
//  HomeController.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/14/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class RootViewController;
@class LoadingView;


@interface HomeController : UIViewController <NSXMLParserDelegate>{
	IBOutlet UIButton *settingsButton;
	UIButton *createButton;
	UIButton *eventsButton;
	UIButton *latestButton;
	RootViewController *rootController;
	NSString *latest_event_id;
	IBOutlet UIImageView *latestTipImage;
	IBOutlet UIImageView *latestHandImage;
	IBOutlet UIActivityIndicatorView *latestActivity;
	//---xml parser stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentNotifyIn, *currentNotifyOut, *currentNotifyEventChange, *currentAppNotifyIn, *currentAppNotifyOut, *currentAppNotifyEventChange;
	NSMutableString *currentEventID, *currentEventName, *currentEventWhen;

	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}

@property (readwrite) bool viewAppeared;

@property (nonatomic, retain) NSMutableData *webData;
@property (nonatomic, retain) NSXMLParser *xmlParser;

@property (nonatomic, retain) IBOutlet UIButton *createButton;
@property (nonatomic, retain) IBOutlet UIButton *eventsButton;
@property (nonatomic, retain) IBOutlet UIButton *latestButton;
@property (nonatomic, retain) RootViewController *rootController;
@property (nonatomic, retain) LoadingView *loadingView;
@property (nonatomic, retain) NSString *latest_event_id;
@property (nonatomic, retain) UIImageView *latestTipImage;
@property (nonatomic, retain) UIImageView *latestHandImage;
@property (nonatomic, retain) UIActivityIndicatorView *latestActivity;
@property (nonatomic, retain) UIButton *settingsButton;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
-(IBAction)createButtonPressed;
-(IBAction)eventsButtonPressed;
-(IBAction)latestButtonPressed;
-(IBAction)settingsButtonPressed;
-(void)checkValidation;
-(void)swapCheckValidation;
@end
