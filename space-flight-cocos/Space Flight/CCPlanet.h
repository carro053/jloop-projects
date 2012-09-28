#import "cocos2d.h"

@interface CCPlanet : CCSprite {
    double radius;
    double density;
    bool antiGravity;
    bool hasMoon;
    CCSprite *moon;
    double moonAngle;
    double startingMoonAngle;
    double xOrbit;
    double yOrbit;
    double moonRadius;
}
@property (readwrite) double radius;
@property (readwrite) double density;
@property (readwrite) bool antiGravity;
@property (readwrite) bool hasMoon;
@property (nonatomic,retain) CCSprite *moon;
@property (readwrite) double moonAngle;
@property (readwrite) double startingMoonAngle;
@property (readwrite) double xOrbit;
@property (readwrite) double yOrbit;
@property (readwrite) double moonRadius;

+(id)spriteWithFile:(NSString*)filename withRadius:(double)radius withDensity:(double)density withAntiGravity:(bool)anti_gravity withMoon:(bool)hasMoon withMoonAngle:(double)moonAngle;

@end