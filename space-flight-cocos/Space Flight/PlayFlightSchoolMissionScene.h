//
//  PlayFlightSchoolMissionScene.h
//  LineDrawing
//
//  Created by Michael Stratford on 4/6/12.
//  Copyright JLOOP 2012. All rights reserved.
//


// When you import this file, you import all the cocos2d classes
#import "cocos2d.h"
#import "CCPanZoomController.h"
#import "PlayFlightSchoolMissionViewController.h"

// HelloWorldLayer
@interface PlayFlightSchoolMissionScene : CCLayer <UIAlertViewDelegate>
{
    bool shipFlying;
    bool startFlying;
    bool outOfFuel;
    bool colorLine;
    bool gravityField;
    NSMutableArray *planets;
    NSMutableArray *astronauts;
    NSMutableArray *items;
    NSMutableArray *wells;
    NSMutableArray *thePath;
    NSMutableArray *shipPath;
    NSMutableArray *pathPoints;
    int total_fuel;
    double fuel_cost;
    double travelTime;
    double shipSpeed;
    double previousShipX;
    double previousShipY;
    double previousPreviousShipX;
    double previousPreviousShipY;
    double shipFlightDuration;
    CCSprite *ship;
    CCPanZoomController *_controller;
    CCLayer *gameLayer;
    PlayFlightSchoolMissionViewController *_playFlightSchoolMissionViewController;
    
}
@property (readwrite) bool shipFlying;
@property (readwrite) bool startFlying;
@property (readwrite) bool outOfFuel;
@property (readwrite) bool colorLine;
@property (readwrite) bool gravityField;
@property (nonatomic,retain) NSMutableArray *planets;
@property (nonatomic,retain) NSMutableArray *astronauts;
@property (nonatomic,retain) NSMutableArray *items;
@property (nonatomic,retain) NSMutableArray *wells;
@property (nonatomic,retain) NSMutableArray *thePath;
@property (nonatomic,retain) NSMutableArray *shipPath;
@property (nonatomic,retain) NSMutableArray *pathPoints;
@property (readwrite) int total_fuel;
@property (readwrite) double fuel_cost;
@property (readwrite) double travelTime;
@property (readwrite) double shipSpeed;
@property (readwrite) double previousShipX;
@property (readwrite) double previousShipY;
@property (readwrite) double previousPreviousShipX;
@property (readwrite) double previousPreviousShipY;
@property (readwrite) double shipFlightDuration;
@property (nonatomic,retain) CCSprite *ship;
@property (nonatomic,retain) CCLayer *gameLayer;
@property (retain) PlayFlightSchoolMissionViewController *playFlightSchoolMissionViewController;

+(CCScene *) sceneWithId:(int)theId viewController:(PlayFlightSchoolMissionViewController *)viewController;

-(void) play;
-(void) setupAstronauts;
-(void) resetAstronauts;
-(void) setupPlanets;
-(void) shipDoneFlying;
-(void) shipCrashed;
-(void) shipRanOutOfFuel;

@end