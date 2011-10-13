//
//  EventListViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/27/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class LoadingView;

enum {
	kEventOff,
	kEventPending,
	kEventNeedsMore,
	kEventOn
};


@interface EventListViewController : UITableViewController <NSXMLParserDelegate>{
	NSMutableArray *eventlist;
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentName, *currentID, *currentActive, *currentWhen, *currentNeed, *currentMembersIn;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}
@property (nonatomic, retain) NSMutableArray *eventlist;
@property (nonatomic, retain) LoadingView *loadingView;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
-(void)refreshData;
@end
