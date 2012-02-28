//
//  SettingsTracker.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/18/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <Foundation/Foundation.h>

#define kFilename	@"dwhefullsettingsdata.plist"


@interface SettingsTracker : NSObject {
	NSString *emailAddress;
	NSString *userName;
	NSString *isValidated;
	NSString *notifyIn;
	NSString *notifyOut;
	NSString *notifyEventChange;
	NSString *appNotifyIn;
	NSString *appNotifyOut;
	NSString *appNotifyEventChange;
	BOOL isInited;
}
@property (nonatomic, retain) NSString *emailAddress;
@property (nonatomic, retain) NSString *userName;
@property (nonatomic, retain) NSString *isValidated;
@property (nonatomic, retain) NSString *notifyIn;
@property (nonatomic, retain) NSString *notifyOut;
@property (nonatomic, retain) NSString *notifyEventChange;
@property (nonatomic, retain) NSString *appNotifyIn;
@property (nonatomic, retain) NSString *appNotifyOut;
@property (nonatomic, retain) NSString *appNotifyEventChange;
@property (nonatomic, readwrite) BOOL isInited;
-(NSString *)dataFilePath;
-(void)saveEmail:(NSString *)email;
-(void)saveValidation:(NSString *)validation;
-(void)saveNotifyIn:(NSString *)notify;
-(void)saveNotifyOut:(NSString *)notify;
-(void)saveNotifyEventChange:(NSString *)notify;
-(void)saveAppNotifyIn:(NSString *)notify;
-(void)saveAppNotifyOut:(NSString *)notify;
-(void)saveAppNotifyEventChange:(NSString *)notify;
-(void)saveUserName:(NSString *)userName;
-(void)initData;
-(void)resetData;

@end
