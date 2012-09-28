//
//  Game.h
//  AppScaffold
//

#import <Foundation/Foundation.h>
#import <UIKit/UIDevice.h>
#import "TouchSheet.h"

@interface Game : SPSprite
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
  @private 
    float mGameWidth;
    float mGameHeight;
}

- (id)initWithWidth:(float)width height:(float)height;
@property (readwrite) bool shipFlying;
@property (readwrite) bool startFlying;
@property (readwrite) bool outOfFuel;
@property (readwrite) bool colorLine;
@property (readwrite) bool gravityField;
@property (nonatomic,strong) NSMutableArray *planets;
@property (nonatomic,strong) NSMutableArray *astronauts;
@property (nonatomic,strong) NSMutableArray *items;
@property (nonatomic,strong) NSMutableArray *wells;
@property (nonatomic,strong) NSMutableArray *thePath;
@property (nonatomic,strong) NSMutableArray *shipPath;
@property (nonatomic,strong) NSMutableArray *pathPoints;
@property (readwrite) int total_fuel;
@property (readwrite) double fuel_cost;
@property (readwrite) double travelTime;
@property (readwrite) double shipSpeed;
@property (readwrite) double previousShipX;
@property (readwrite) double previousShipY;
@property (readwrite) double previousPreviousShipX;
@property (readwrite) double previousPreviousShipY;
@property (readwrite) double shipFlightDuration;
@property (nonatomic, assign) float gameWidth;
@property (nonatomic, assign) float gameHeight;

@end
