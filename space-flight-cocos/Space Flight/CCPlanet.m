#import "CCPlanet.h"
#import "CCAnimate+SequenceLoader.h"

@implementation CCPlanet

@synthesize radius;
@synthesize density;
@synthesize antiGravity;
@synthesize hasMoon;
@synthesize moon;
@synthesize moonAngle;
@synthesize startingMoonAngle;
@synthesize xOrbit;
@synthesize yOrbit;
@synthesize moonRadius;


-(id) init {
    if((self=[super init])){
        radius = 35.0;
        density = 0.003;
        antiGravity = NO;
        hasMoon = NO;
        moonAngle = 0.0;
        xOrbit = 11.0 / 8.0;
        yOrbit = 3.0 / 2.0;
        moonRadius = 2.0 / 7.0;
    }
    return self;
}

+(id)spriteWithFile:(NSString*)filename withRadius:(double)radius withDensity:(double)density withAntiGravity:(bool)anti_gravity withMoon:(bool)hasMoon withMoonAngle:(double)moonAngle
{
    CCPlanet *planet;
    /*if(anti_gravity)
     {
     CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"anti_gravity.png"];
     planet = [self spriteWithSpriteFrame:frame];
     }else if(density <= 0.005)
     {
     CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"planet_5.png"];
     planet = [self spriteWithSpriteFrame:frame];
     }else if(density <= 0.007)
     {
     CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"planet_3.png"];
     planet = [self spriteWithSpriteFrame:frame];
     }else if(density <= 0.009)
     {
     CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"planet_1.png"];
     planet = [self spriteWithSpriteFrame:frame];
     }else if(density <= 0.011)
     {
     CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"planet_2.png"];
     planet = [self spriteWithSpriteFrame:frame];
     }else if(density <= 0.013)
     {
     CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"planet_4.png"];
     planet = [self spriteWithSpriteFrame:frame];
     }else if(density <= 0.015)
     {
     CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"planet_6.png"];
     planet = [self spriteWithSpriteFrame:frame];
     }*/
    if(anti_gravity)
    {
        CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"anti_gravity.png"];
        planet = [self spriteWithSpriteFrame:frame];
    }else{
        CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:@"planet_4.png"];
        planet = [self spriteWithSpriteFrame:frame];
    }
    planet.color = ccc3(ceil(arc4random() % 205) + 50, ceil(arc4random() % 205) + 50, ceil(arc4random() % 205) + 50);
    if(hasMoon)
    {
        planet.startingMoonAngle = moonAngle;
        CCSprite *newmoon = [CCSprite spriteWithSpriteFrameName:@"moon.png"];
        newmoon.position = ccp([planet boundingBox].size.width / 2,[planet boundingBox].size.height * 1.25);
        //newmoon.color = ccc3(ceil(arc4random() % 205) + 50, ceil(arc4random() % 205) + 50, ceil(arc4random() % 205) + 50);
        planet.moon = newmoon;
        [planet addChild:newmoon];
        CCSprite *moonpulse = [CCSprite spriteWithSpriteFrameName:@"gravity_pulse_1.png"];
        id pulseAnimate = [CCRepeatForever actionWithAction:[CCAnimate actionWithSpriteSequence:@"gravity_pulse_%d.png" numFrames:9 delay:0.085 restoreOriginalFrame:NO]];
        [moonpulse runAction:pulseAnimate];
        moonpulse.scale = 0.2127 * radius / 70.0;
        moonpulse.position = ccp( -3.0, -4.0);
        moonpulse.opacity = 100;
        moonpulse.color = ccc3(255,255,0);
        moonpulse.anchorPoint = ccp(0,0);
        [newmoon addChild:moonpulse z:-1];
    }
    
    //double pulse_speed = 0.12 - (density - 0.003) / 0.012 * 0.07;
    double pulse_speed = 0.085;
    //double pulse_opacity = 20 + 230 * (density - 0.003) / 0.012;
    //double pulse_opacity = (density - 0.003) / 0.012 * 30 + 20;
    double pulse_opacity = 255;
    double density_percentage = 1 - (density - 0.003) / 0.012;
    ccColor3B pulse_color;
    
    
    if(density_percentage > 0.5)
    {
        pulse_color = ccc3((255 - 255 * (density_percentage - 0.5) / 0.5),255,0);
    }else{
        pulse_color = ccc3(255,(255 * density_percentage / 0.5),0);
    }
    pulse_color = ccc3(50 + (150 * density_percentage),50 + (150 * density_percentage),255);
    //planet.color = pulse_color;
    //pulse_color = ccc3(255,255,255);
    if(anti_gravity)
    {
        //id rotate_planet = [CCRepeatForever actionWithAction:[CCRotateBy actionWithDuration:0.001 angle:1]];
        //[planet runAction:rotate_planet];
        CCSprite *pulse = [CCSprite spriteWithSpriteFrameName:@"anti_gravity_pulse_1.png"];
         id pulseAnimate = [CCRepeatForever actionWithAction:[CCAnimate actionWithSpriteSequence:@"anti_gravity_pulse_%d.png" numFrames:9 delay:pulse_speed restoreOriginalFrame:NO]];
         [pulse runAction:pulseAnimate];
         pulse.position = ccp( 72.0, 72.0);
         pulse.opacity = pulse_opacity;
         pulse.color = pulse_color;
         [planet addChild:pulse z:-1];
    }else{
        CCSprite *pulse = [CCSprite spriteWithSpriteFrameName:@"gravity_pulse_1.png"];
        id pulseAnimate = [CCRepeatForever actionWithAction:[CCAnimate actionWithSpriteSequence:@"gravity_pulse_%d.png" numFrames:9 delay:pulse_speed restoreOriginalFrame:NO]];
        [pulse runAction:pulseAnimate];
        pulse.position = ccp( 71.0, 71.0);
        pulse.opacity = pulse_opacity;
        pulse.color = pulse_color;
        [planet addChild:pulse z:-1];
    }
	return planet;
}
-(void) draw {
    moon.position = ccp(70 + cos(moonAngle) * 70 * xOrbit, 70 + sin(moonAngle) * 70 * yOrbit);
    [super draw];
}

@end