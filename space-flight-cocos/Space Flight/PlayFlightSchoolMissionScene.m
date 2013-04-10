//
//  HelloWorldLayer.m
//  LineDrawing
//
//  Created by Michael Stratford on 4/6/12.
//  Copyright JLOOP 2012. All rights reserved.
//


// Import the interfaces
#import "PlayFlightSchoolMissionScene.h"
#import "MoveArray.m"
#import "CCPlanet.h"
#import "CCItem.h"
#import "CCAnimate+SequenceLoader.h"
#import "CCGravityField.h"
#import "PathLayer.h"
#import "SBJson.h"
#import "UIDevice+IdentifierAddition.h"
#import "Constants.h"

int missionId;

NSMutableArray *astronautsData;
NSMutableArray *beaconsData;
NSMutableArray *itemsData;
NSMutableArray *wellsData;
NSMutableArray *planetsData;
int movingPoint;
BOOL newPoint;
BOOL startFlying;
bool panning;
bool startup;
bool shipOutOfFuel;
UITouch *panningTouch;
PathLayer *pathLayer;

CCSprite *fuelTank;
CCSprite *space;
CCSprite *selectedSprite;

double previousProbeX;
double previousProbeY;
double probeSpeed;
CCSprite *probe;
bool probeActive;
bool placingProbe;

CCSprite *start;
NSArray *startData;
CCSprite *end;
NSArray *endData;

NSDate *lastProbeMoveDate;
double lastProbeMoveSpeed;

@implementation PlayFlightSchoolMissionScene

@synthesize gameLayer;
@synthesize shipSpeed;
@synthesize previousShipX;
@synthesize previousShipY;
@synthesize previousPreviousShipX;
@synthesize previousPreviousShipY;
@synthesize shipFlightDuration;
@synthesize shipFlying;
@synthesize startFlying;
@synthesize outOfFuel;
@synthesize colorLine;
@synthesize astronauts;
@synthesize items;
@synthesize wells;
@synthesize planets;
@synthesize thePath;
@synthesize shipPath;
@synthesize pathPoints;
@synthesize total_fuel;
@synthesize fuel_cost;
@synthesize travelTime;
@synthesize ship;
@synthesize gravityField;

@synthesize playFlightSchoolMissionViewController = _playMissionViewController;

+(CCScene *) sceneWithId:(int)theId viewController:(PlayFlightSchoolMissionViewController *)viewController
{
    missionId = theId;
	// 'scene' is an autorelease object.
	CCScene *scene = [CCScene node];
	
	// 'layer' is an autorelease object.
	PlayFlightSchoolMissionScene *layer = [PlayFlightSchoolMissionScene node];
    layer.playFlightSchoolMissionViewController = viewController;
	
	// add layer as a child to scene
	[scene addChild: layer];
	
	// return the scene
	return scene;
}

// on "init" you need to initialize your instance
-(id) init
{
	// always call "super" init
	// Apple recommends to re-assign "self" with the "super" return value
	if( (self=[super init])) {
        
        lastProbeMoveDate = [[NSDate alloc] init];
        
        [TestFlight passCheckpoint:[NSString stringWithFormat:@"Play Flight School Mission:%d",missionId]];
        
		astronauts = [[NSMutableArray alloc] init];
		items = [[NSMutableArray alloc] init];
		wells = [[NSMutableArray alloc] init];
		planets = [[NSMutableArray alloc] init];
		shipPath = [[NSMutableArray alloc] init];
		thePath = [[NSMutableArray alloc] init];
		pathPoints = [[NSMutableArray alloc] init];
        
        
        
        NSString* plistPath = [[NSBundle mainBundle] pathForResource:@"FlightSchool" ofType:@"plist"];
        NSDictionary *pdict = [NSDictionary dictionaryWithContentsOfFile:plistPath];
        NSMutableArray *missionArray = [pdict objectForKey:@"flightSchoolArray"];
        NSDictionary *dict = [missionArray objectAtIndex:missionId];
        
        startData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:240.0],[NSNumber numberWithDouble:160.0],nil];
        endData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:784.0],[NSNumber numberWithDouble:558.0],nil];
        //read plist
        NSArray *keys = [dict allKeys];
        //loop through keys of plist
        for(NSString *key in keys)
        {
            if([key isEqualToString:@"astronauts"])
                astronautsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"beacons"])
                beaconsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"items"])
                itemsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"wells"])
                wellsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"planets"])
                planetsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"total_fuel"])
                total_fuel = [[dict objectForKey:key] intValue];
            if([key isEqualToString:@"startPoint"])
                startData = [dict objectForKey:key];
            if([key isEqualToString:@"endPoint"])
                endData = [dict objectForKey:key];
        }
        [self loadScene];
	}
	return self;
}
-(void) loadScene {
    
    BOOL iPad = NO;
#ifdef UI_USER_INTERFACE_IDIOM
    iPad = (UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPad);
#endif
    double width = [[UIScreen mainScreen] bounds].size.width;
    double height = [[UIScreen mainScreen] bounds].size.height;
    
    [CCTexture2D PVRImagesHavePremultipliedAlpha:YES];
    [[CCSpriteFrameCache sharedSpriteFrameCache] addSpriteFramesWithFile:@"general.plist"];
    
    gameLayer = [CCLayer node];
    
    [gameLayer setContentSize:CGSizeMake(height, width - 44)];
    
    [self addChild:gameLayer z:1];
    
    pathLayer = [PathLayer layerWithParent:self];
    [gameLayer addChild:pathLayer z:50];
    
    colorLine = YES;
    
    space = [CCSprite spriteWithFile:@"stars.jpg"];
    space.anchorPoint = ccp(0.5,0.5);
    space.position = ccp(512,384);
    [gameLayer addChild:space z:0];
    
    // the pan/zoom controller
    _controller = [[CCPanZoomController controllerWithNode:gameLayer] retain];
    if(iPad)
    {
        _controller.zoomInLimit = 4.0f;
    }else{
        _controller.zoomInLimit = 2.0f;
    }
    CGRect windowRect = CGRectMake(0, 0, height, width - 44);
    [_controller setWindowRect:windowRect];
    CGRect boundingRect = CGRectMake(0, 0, 1024, 724);
    [_controller setBoundingRect:boundingRect];
    [_controller updatePosition:CGPointMake(512, 362)];
    [_controller optimalZoomOutLimit];
    _controller.zoomOutLimit = [_controller optimalZoomOutLimit];
    _controller.zoomRate = 1/200.0f;
    [_controller zoomOutOnPoint:CGPointMake(512,362) duration:0];
    startup = YES;
    
    [_controller enableWithTouchPriority:0 swallowsTouches:NO];
    panning = YES;
    
    CCGravityField *gravityFieldLayer = [CCGravityField layerWithParent:self];
    [gameLayer addChild:gravityFieldLayer z:1];
    gravityField = NO;
    
    [self setupAstronauts];
    [self setupItems];
    [self setupWells];
    [self setupPlanets];
    
    probe = [CCSprite spriteWithSpriteFrameName:@"probe.png"];
    probe.visible = NO;
    [gameLayer addChild:probe z:9];
    probeActive = NO;
    placingProbe = NO;
    
    start = [CCSprite spriteWithSpriteFrameName:@"space_station.png"];
    start.position = ccp([[startData objectAtIndex:0] doubleValue],[[startData objectAtIndex:1] doubleValue]);
    start.scale = 2.0;
    start.tag = 5;
    [gameLayer addChild:start z:50];
    [thePath addObject:start];
    
    
    [self setupBeacons];
    
    end = [CCSprite spriteWithSpriteFrameName:@"space_station.png"];
    end.position = ccp([[endData objectAtIndex:0] doubleValue],[[endData objectAtIndex:1] doubleValue]);
    end.scale = 2.0;
    end.tag = 5;
    [gameLayer addChild:end z:50];
    [thePath addObject:end];
    [self setIsTouchEnabled:YES];
    ship = [CCSprite spriteWithSpriteFrameName:@"fury.png"];
    ship.position = start.position;
    ship.visible = NO;
    [gameLayer addChild:ship z:10];
    shipFlying = NO;
    startFlying = NO;
    
    movingPoint = -1;
    
    fuel_cost = maxFuel - total_fuel;
    
    self.playFlightSchoolMissionViewController.fuelIndicator.progress = 0;
    [self.playFlightSchoolMissionViewController.fuelIndicator setProgress:total_fuel / maxFuel animated:YES];
    [self schedule:@selector(tick:) interval: 1/60.0f];
}
- (void) onEnterTransitionDidFinish
{
    [self.playFlightSchoolMissionViewController.fuelIndicator setProgress:((total_fuel) / maxFuel) animated:YES];
}

- (NSString *)stringWithUrl:(NSURL *)url
{
	NSURLRequest *urlRequest = [NSURLRequest requestWithURL:url
                                                cachePolicy:NSURLRequestReloadIgnoringCacheData
                                            timeoutInterval:5];
    // Fetch the JSON response
	NSData *urlData;
	NSURLResponse *response;
	NSError *error;
    
	// Make synchronous request
	urlData = [NSURLConnection sendSynchronousRequest:urlRequest
                                    returningResponse:&response
                                                error:&error];
    
 	// Construct a String around the Data from the response
	return [[NSString alloc] initWithData:urlData encoding:NSUTF8StringEncoding];
}

- (id) objectWithUrl:(NSURL *)url
{
	SBJsonParser *jsonParser = [SBJsonParser new];
	NSString *jsonString = [self stringWithUrl:url];
	// Parse the JSON into an Object
	return [jsonParser objectWithString:jsonString error:NULL];
}

- (NSDictionary *) getOnlineMission
{
	id response = [self objectWithUrl:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/missions/getMission/%d",missionId]]];
	NSDictionary *feed = (NSDictionary *)response;
	return feed;
}

-(void) draw {   
    if(startup)
    {
        [_controller zoomOutOnPoint:CGPointMake(512,362) duration:0];
        startup = NO;
    }
    if(self.playFlightSchoolMissionViewController.play)
    {
        self.playFlightSchoolMissionViewController.play = NO;
        [self play];
    }
    
    if(self.playFlightSchoolMissionViewController.gravity)
    {
        self.playFlightSchoolMissionViewController.gravity = NO;
        gravityField = !gravityField;
        if(gravityField)
        {
            self.playFlightSchoolMissionViewController.fidoButton.title = @"Hide F.I.D.O.";
        }else{
            self.playFlightSchoolMissionViewController.fidoButton.title = @"Show F.I.D.O.";
        }
    }
    [super draw];
}

-(void) tick: (ccTime) dt
{
    if(probeActive)
        [self updateProbe:dt];
    [self updateShip:dt];
}

-(void) updateProbe:(ccTime)dt {
    double travel_time = dt;
    double new_v_x = 0.0;
    double new_v_y = 0.0;
    if(sqrt(pow(probe.position.x - previousProbeX, 2) + pow(probe.position.y - previousProbeY, 2)) != 0)
    {
        double u_x = (probe.position.x - previousProbeX) / sqrt(pow(probe.position.x - previousProbeX, 2) + pow(probe.position.y - previousProbeY, 2));
        double u_y = (probe.position.y - previousProbeY) / sqrt(pow(probe.position.x - previousProbeX, 2) + pow(probe.position.y - previousProbeY, 2));
        new_v_x = u_x * probeSpeed;
        new_v_y = u_y * probeSpeed;
    }
    double gx = 0.0;
    double gy = 0.0;
    for (CCPlanet *planet in planets) {
        if(!planet.antiGravity && pow(probe.position.x-planet.position.x,2) + pow(probe.position.y - planet.position.y,2) < pow(planet.radius,2))
            probeActive = NO;
        double planetX = planet.position.x;
        double planetY = planet.position.y;
        double planetRadius = planet.radius;
        double planetDensity = planet.density;
        double planetMass = planetDensity * 4 / 3 * M_PI * pow(planetRadius, 3);
        double gravity = gConstant * planetMass * shipMass / pow(sqrt(pow(probe.position.x - planetX,2) + pow(probe.position.y - planetY,2)) * gDistanceConstant,2);
        if(planet.antiGravity)
            gravity = gravity * -1.0;
        double g_x = (probe.position.x - planetX) / sqrt(pow(probe.position.x - planetX, 2) + pow(probe.position.y - planetY, 2));
        double g_y = (probe.position.y - planetY) / sqrt(pow(probe.position.x - planetX, 2) + pow(probe.position.y - planetY, 2));
        gx += g_x * gravity;
        gy += g_y * gravity;
        if (planet.hasMoon) {
            if(pow(probe.position.x-(planet.position.x + cos(planet.moonAngle) * planet.radius * planet.xOrbit),2) + pow(probe.position.y - (planet.position.y + sin(planet.moonAngle) * planet.radius * planet.yOrbit),2) < pow(planet.radius * planet.moonRadius,2))
                probeActive = NO;
            double moonX = planet.position.x + cos(planet.moonAngle) * planet.radius * planet.xOrbit;
            double moonY = planet.position.y + sin(planet.moonAngle) * planet.radius * planet.yOrbit;
            double moonRadius = planet.radius * planet.moonRadius;
            double moonDensity = moon_density;
            double moonMass = moonDensity * 4 / 3 * M_PI * pow(moonRadius, 3);
            double moonGravity = gConstant * moonMass * shipMass / pow(sqrt(pow(probe.position.x - moonX,2) + pow(probe.position.y - moonY,2)) * gDistanceConstant,2);
            double mg_x = (probe.position.x - moonX) / sqrt(pow(probe.position.x - moonX, 2) + pow(probe.position.y - moonY, 2));
            double mg_y = (probe.position.y - moonY) / sqrt(pow(probe.position.x - moonX, 2) + pow(probe.position.y - moonY, 2));
            gx += mg_x * moonGravity;
            gy += mg_y * moonGravity;
        }
    }
    for (CCSprite *well in wells) {
        double wellX = well.position.x;
        double wellY = well.position.y;
        int wellPower = well.tag;
        double gravity = gConstant * wellPower * shipMass / pow(sqrt(pow(probe.position.x - wellX,2) + pow(probe.position.y - wellY,2)) * gDistanceConstant,2);
        double g_x = (probe.position.x - wellX) / sqrt(pow(probe.position.x - wellX, 2) + pow(probe.position.y - wellY, 2));
        double g_y = (probe.position.y - wellY) / sqrt(pow(probe.position.x - wellX, 2) + pow(probe.position.y - wellY, 2));
        gx += g_x * gravity;
        gy += g_y * gravity;
    }
    if(probeActive)
    {
        
        [probe setRotation:(probe.rotation + probeSpeed * dt)];
        new_v_x -= gx / shipMass * travel_time;
        new_v_y -= gy / shipMass * travel_time;
        
        double new_x = new_v_x * travel_time;
        double new_y = new_v_y * travel_time;
        probeSpeed = sqrt(pow(new_v_x,2) + pow(new_v_y,2));
        previousProbeX = probe.position.x;
        previousProbeY = probe.position.y;
        probe.position = ccp(probe.position.x + new_x,probe.position.y + new_y);
    }
}
-(void) updateShip:(ccTime)dt {
    if(shipFlying)
    {
        shipFlightDuration += dt;
        
        for (CCPlanet *planet in planets) {
            if(!planet.antiGravity && pow(ship.position.x-planet.position.x,2) + pow(ship.position.y - planet.position.y,2) < pow(planet.radius,2))
                [self shipCrashed];
            if(planet.hasMoon)
            {
                planet.moonAngle = planet.startingMoonAngle + M_PI / planet.radius * (planet.density + 0.012) / 0.03 / 2.0 * 60.0 * shipFlightDuration;
                if(pow(ship.position.x-(planet.position.x + cos(planet.moonAngle) * planet.radius * planet.xOrbit),2) + pow(ship.position.y - (planet.position.y + sin(planet.moonAngle) * planet.radius * planet.yOrbit),2) < pow(planet.radius * planet.moonRadius,2))
                    [self shipCrashed];
            }
        }
        if(shipOutOfFuel)
        {
            double travel_time = dt;
            double new_v_x = 0.0;
            double new_v_y = 0.0;
            if(sqrt(pow(ship.position.x - previousShipX, 2) + pow(ship.position.y - previousShipY, 2)) != 0)
            {
                double u_x = (ship.position.x - previousShipX) / sqrt(pow(ship.position.x - previousShipX, 2) + pow(ship.position.y - previousShipY, 2));
                double u_y = (ship.position.y - previousShipY) / sqrt(pow(ship.position.x - previousShipX, 2) + pow(ship.position.y - previousShipY, 2));
                new_v_x = u_x * shipSpeed;
                new_v_y = u_y * shipSpeed;
            }
            double gx = 0.0;
            double gy = 0.0;
            for (CCPlanet *planet in planets) {
                if(!planet.antiGravity && pow(ship.position.x-planet.position.x,2) + pow(ship.position.y - planet.position.y,2) < pow(planet.radius,2))
                    [self shipCrashed];
                double planetX = planet.position.x;
                double planetY = planet.position.y;
                double planetRadius = planet.radius;
                double planetDensity = planet.density;
                double planetMass = planetDensity * 4 / 3 * M_PI * pow(planetRadius, 3);
                double gravity = gConstant * planetMass * shipMass / pow(sqrt(pow(ship.position.x - planetX,2) + pow(ship.position.y - planetY,2)) * gDistanceConstant,2);
                if(planet.antiGravity)
                    gravity = gravity * -1.0;
                double g_x = (ship.position.x - planetX) / sqrt(pow(ship.position.x - planetX, 2) + pow(ship.position.y - planetY, 2));
                double g_y = (ship.position.y - planetY) / sqrt(pow(ship.position.x - planetX, 2) + pow(ship.position.y - planetY, 2));
                gx += g_x * gravity;
                gy += g_y * gravity;
                if (planet.hasMoon) {
                    if(pow(ship.position.x-(planet.position.x + cos(planet.moonAngle) * planet.radius * planet.xOrbit),2) + pow(ship.position.y - (planet.position.y + sin(planet.moonAngle) * planet.radius * planet.yOrbit),2) < pow(planet.radius * planet.moonRadius,2))
                        [self shipCrashed];
                    double currentMoonAngle = planet.moonAngle;
                    double moonX = planet.position.x + cos(currentMoonAngle) * planet.radius * planet.xOrbit;
                    double moonY = planet.position.y + sin(currentMoonAngle) * planet.radius * planet.yOrbit;
                    double moonRadius = planet.radius * planet.moonRadius;
                    double moonDensity = moon_density;
                    double moonMass = moonDensity * 4 / 3 * M_PI * pow(moonRadius, 3);
                    double moonGravity = gConstant * moonMass * shipMass / pow(sqrt(pow(ship.position.x - moonX,2) + pow(ship.position.y - moonY,2)) * gDistanceConstant,2);
                    double mg_x = (ship.position.x - moonX) / sqrt(pow(ship.position.x - moonX, 2) + pow(ship.position.y - moonY, 2));
                    double mg_y = (ship.position.y - moonY) / sqrt(pow(ship.position.x - moonX, 2) + pow(ship.position.y - moonY, 2));
                    gx += mg_x * moonGravity;
                    gy += mg_y * moonGravity;
                }
            }
            for (CCSprite *well in wells) {
                double wellX = well.position.x;
                double wellY = well.position.y;
                int wellPower = well.tag;
                double gravity = gConstant * wellPower * shipMass / pow(sqrt(pow(ship.position.x - wellX,2) + pow(ship.position.y - wellY,2)) * gDistanceConstant,2);
                double g_x = (ship.position.x - wellX) / sqrt(pow(ship.position.x - wellX, 2) + pow(ship.position.y - wellY, 2));
                double g_y = (ship.position.y - wellY) / sqrt(pow(ship.position.x - wellX, 2) + pow(ship.position.y - wellY, 2));
                gx += g_x * gravity;
                gy += g_y * gravity;
            }
            double distance = sqrt(pow(ship.position.x-end.position.x,2) + pow(ship.position.y - end.position.y,2));
            if(distance > abyssDistance)
                shipOutOfFuel = NO;
            if(shipOutOfFuel)
            {
                [ship setRotation:(ship.rotation + shipSpeed * dt)];
                new_v_x -= gx / shipMass * travel_time;
                new_v_y -= gy / shipMass * travel_time;
                
                double new_x = new_v_x * travel_time;
                double new_y = new_v_y * travel_time;
                shipSpeed = sqrt(pow(new_v_x,2) + pow(new_v_y,2));
                previousShipX = ship.position.x;
                previousShipY = ship.position.y;
                ship.position = ccp(ship.position.x + new_x,ship.position.y + new_y);
            }else{
                [self resetMission];
                UIView* view = [[CCDirector sharedDirector] openGLView];
                UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Mission Failure" message: @"You have ran out of fuel and drifted off into deep space!" delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
                [view addSubview: myAlertView];
                [myAlertView show];
                [myAlertView release];
            }
        }else if(shipFlying)
        {
            bool shipUpdated = NO;
            for(NSDictionary *shipPoint in shipPath)
            {
                if([[shipPoint objectForKey:@"total_travel_time"] doubleValue] >= shipFlightDuration)
                {
                    previousPreviousShipX = previousShipX;
                    previousPreviousShipY = previousShipY;
                    previousShipX = ship.position.x;
                    previousShipY = ship.position.y;
                    shipSpeed = [[shipPoint objectForKey:@"speed"] doubleValue];
                    ship.position = CGPointMake([[shipPoint objectForKey:@"x"] doubleValue], [[shipPoint objectForKey:@"y"] doubleValue]);
                    [ship setRotation:[[shipPoint objectForKey:@"angle"] floatValue]];
                    
                    if([[shipPoint objectForKey:@"thrust_power"] doubleValue] > 0.005)
                        [self createExplosionX:ship.position.x y:ship.position.y power:[[shipPoint objectForKey:@"thrust_power"] doubleValue] angle:[[shipPoint objectForKey:@"thrust_angle"] doubleValue] dt:dt];
                    
                    [self.playFlightSchoolMissionViewController.fuelIndicator setProgress:((maxFuel - [[shipPoint objectForKey:@"total_fuel_used"] doubleValue]) / maxFuel) animated:YES];
                    travelTime = [[shipPoint objectForKey:@"total_travel_time"] doubleValue];
                    fuel_cost = [[shipPoint objectForKey:@"total_fuel_used"] doubleValue];
                    shipUpdated = YES;
                    break;
                }
            }
            if(shipUpdated)
            {
                if(fuel_cost > maxFuel)
                {
                    [self shipRanOutOfFuel];
                }else{
                    
                    for (CCSprite* astronaut in astronauts)
                    {
                        if(astronaut.visible)
                        {
                            for(NSDictionary *shipPoint in shipPath)
                            {
                                if([[shipPoint objectForKey:@"total_travel_time"] doubleValue] < shipFlightDuration)
                                {
                                    double separation = sqrt(pow(astronaut.position.x - [[shipPoint objectForKey:@"x"] doubleValue],2) + pow(astronaut.position.y - [[shipPoint objectForKey:@"y"] doubleValue],2));
                                    if(separation < saveThreshold)
                                    {
                                        astronaut.visible = NO;
                                        break;
                                    }
                                }else{
                                    break;
                                }
                            }
                        }
                    }
                    
                    for (CCItem* item in items)
                    {
                        if(item.visible)
                        {
                            for(NSDictionary *shipPoint in shipPath)
                            {
                                if([[shipPoint objectForKey:@"total_travel_time"] doubleValue] < shipFlightDuration)
                                {
                                    double separation = sqrt(pow(item.position.x - [[shipPoint objectForKey:@"x"] doubleValue],2) + pow(item.position.y - [[shipPoint objectForKey:@"y"] doubleValue],2));
                                    if(separation < saveThreshold)
                                    {
                                        item.visible = NO;
                                        break;
                                    }
                                }else{
                                    break;
                                }
                            }
                        }
                    }
                    for (CCPlanet *planet in planets) {
                        if(!planet.antiGravity && pow(ship.position.x-planet.position.x,2) + pow(ship.position.y - planet.position.y,2) < pow(planet.radius,2))
                            [self shipCrashed];
                        if(planet.hasMoon)
                        {
                            if(pow(ship.position.x-(planet.position.x + cos(planet.moonAngle) * planet.radius * planet.xOrbit),2) + pow(ship.position.y - (planet.position.y + sin(planet.moonAngle) * planet.radius * planet.yOrbit),2) < pow(planet.radius * planet.moonRadius,2))
                                [self shipCrashed];
                        }
                    }
                }
            }else{
                [self shipDoneFlying];
            }
        }
    }
}
-(void) createExplosionX:(double)x y:(double)y power:(double)power angle:(double)angle dt:(double) dt
{
    angle = MIN(225,MAX(135,angle));
    CCParticleMeteor *left_emitter = [[CCParticleMeteor alloc] initWithTotalParticles:ceil(20.0 * shipSpeed / 125.0 + 50.0)];
    CCParticleMeteor *center_emitter = [[CCParticleMeteor alloc] initWithTotalParticles:ceil(20.0 * shipSpeed / 125.0 + 50.0)];
    CCParticleMeteor *right_emitter = [[CCParticleMeteor alloc] initWithTotalParticles:ceil(20.0 * shipSpeed / 125.0 + 50.0)];
    
    [left_emitter resetSystem];
    [center_emitter resetSystem];
    [right_emitter resetSystem];
    
    left_emitter.duration = dt;
    center_emitter.duration = dt;
    right_emitter.duration = dt;
    
    left_emitter.gravity = CGPointZero;
    center_emitter.gravity = CGPointZero;
    right_emitter.gravity = CGPointZero;
    
    left_emitter.angle = 180.0;
    left_emitter.angleVar = 0.0;
    center_emitter.angle = 180.0;
    center_emitter.angleVar = 0.0;
    right_emitter.angle = 180.0;
    right_emitter.angleVar = 0.0;
    
    left_emitter.speed = 80;
    left_emitter.speedVar = 10;
    center_emitter.speed = 80;
    center_emitter.speedVar = 10;
    right_emitter.speed = 80;
    right_emitter.speedVar = 10;
    
    left_emitter.radialAccel = 0;
    left_emitter.radialAccelVar = 0;
    center_emitter.radialAccel = 0;
    center_emitter.radialAccelVar = 0;
    right_emitter.radialAccel = 0;
    right_emitter.radialAccelVar = 0;
    
    left_emitter.tangentialAccel = 0;
    left_emitter.tangentialAccelVar = 0;
    center_emitter.tangentialAccel = 0;
    center_emitter.tangentialAccelVar = 0;
    right_emitter.tangentialAccel = 0;
    right_emitter.tangentialAccelVar = 0;
    
    left_emitter.life = 0.25 + 0.3 * power;
    left_emitter.lifeVar = 0.0;
    center_emitter.life = 0.25 + 0.3 * power;
    center_emitter.lifeVar = 0.0;
    right_emitter.life = 0.25 + 0.3 * power;
    right_emitter.lifeVar = 0.0;
    
    left_emitter.startSpin = 0;
    left_emitter.startSpinVar = 0;
    left_emitter.endSpin = 0;
    left_emitter.endSpinVar = 0;
    center_emitter.startSpin = 0;
    center_emitter.startSpinVar = 0;
    center_emitter.endSpin = 0;
    center_emitter.endSpinVar = 0;
    right_emitter.startSpin = 0;
    right_emitter.startSpinVar = 0;
    right_emitter.endSpin = 0;
    right_emitter.endSpinVar = 0;
    
    left_emitter.startSize = 2.0;
    left_emitter.startSizeVar = 0.0;
    left_emitter.endSize = 0.0;
    left_emitter.endSizeVar = 0.0;
    center_emitter.startSize = 2.0;
    center_emitter.startSizeVar = 0.0;
    center_emitter.endSize = 0.0;
    center_emitter.endSizeVar = 0.0;
    right_emitter.startSize = 2.0;
    right_emitter.startSizeVar = 0.0;
    right_emitter.endSize = 0.0;
    right_emitter.endSizeVar = 0.0;
    
    if(angle <= 165)
    {
        left_emitter.startSize = MIN(20.0,20.0 * power + 2.0);
        left_emitter.angle = angle;
    }else if(angle < 195)
    {
        center_emitter.startSize = MIN(20.0,20.0 * power + 2.0);
        center_emitter.angle = angle;
    }else{
        right_emitter.startSize = MIN(20.0,20.0 * power + 2.0);
        right_emitter.angle = angle;
    }
    
    left_emitter.emissionRate = left_emitter.totalParticles/left_emitter.life;
    center_emitter.emissionRate = center_emitter.totalParticles/center_emitter.life;
    right_emitter.emissionRate = right_emitter.totalParticles/right_emitter.life;
    
    left_emitter.blendAdditive = YES;
    center_emitter.blendAdditive = YES;
    right_emitter.blendAdditive = YES;
    
    left_emitter.positionType = kCCPositionTypeRelative;
    center_emitter.positionType = kCCPositionTypeRelative;
    right_emitter.positionType = kCCPositionTypeRelative;
    
    left_emitter.position = ccp(4,16);
    center_emitter.position = ccp(2,9);
    right_emitter.position = ccp(4,2);
    
    [ship addChild:left_emitter z:-1];
    [ship addChild:center_emitter z:-1];
    [ship addChild:right_emitter z:-1];
    
    left_emitter.autoRemoveOnFinish = YES;
    center_emitter.autoRemoveOnFinish = YES;
    right_emitter.autoRemoveOnFinish = YES;
    
    [left_emitter release];
    [center_emitter release];
    [right_emitter release];
}

-(void) setupAstronauts {
    for(NSDictionary *astro in astronautsData)
    {
        CCSprite *astronaut = [CCSprite spriteWithSpriteFrameName:@"astronaut.png"];
        astronaut.position = ccp([[astro objectForKey:@"x"] doubleValue],[[astro objectForKey:@"y"] doubleValue]);
        [astronaut setRotation:arc4random() % 360];
        astronaut.tag = 1;
        [astronauts addObject:astronaut];
        [gameLayer addChild:astronaut z:3];
    }
}
-(void) setupBeacons {
    for(NSDictionary *beac in beaconsData)
    {
        CCSprite *beacon = [CCSprite spriteWithSpriteFrameName:@"beacon_1.png"];
        id beaconAction = [CCRepeatForever actionWithAction:[CCAnimate actionWithSpriteSequence:@"beacon_%d.png" numFrames:2 delay:0.25 restoreOriginalFrame:NO]];
        [beacon runAction:beaconAction];
        beacon.position = ccp([[beac objectForKey:@"x"] doubleValue],[[beac objectForKey:@"y"] doubleValue]);
        [pathLayer addChild:beacon z:50];
        [thePath addObject:beacon];
    }
}

-(void) setupItems {
    for(NSDictionary *theItem in itemsData)
    {
        CCItem *item = [CCItem spriteWithSpriteFrameName:@"fuel.png" andType:@"Fuel"];
        item.position = ccp([[theItem objectForKey:@"x"] doubleValue],[[theItem objectForKey:@"y"] doubleValue]);
        [item setRotation:arc4random() % 360];
        [items addObject:item];
        [gameLayer addChild:item z:3];
    }
}

-(void) setupWells {
    for(NSDictionary *theWell in wellsData)
    {
        CCSprite *well = [CCSprite spriteWithSpriteFrameName:@"gravity_well.png"];
        well.position = ccp([[theWell objectForKey:@"x"] doubleValue],[[theWell objectForKey:@"y"] doubleValue]);
        well.tag = [[theWell objectForKey:@"power"] intValue];
        [well setRotation:arc4random() % 360];
        [wells addObject:well];
        [gameLayer addChild:well z:3];
    }
}
-(void) resetAstronauts {
    for (CCSprite *astronaut in astronauts)
    {
        astronaut.visible = YES;
    }
}

-(void) resetItems {
    for (CCItem *item in items)
    {
        item.visible = YES;
        item.tag = 1;
    }
}
-(void) setupPlanets {
    for(NSDictionary *plan in planetsData)
    {
        CCPlanet *planet = [CCPlanet spriteWithFile:@"planet.png" withRadius:[[plan objectForKey:@"radius"] doubleValue] withDensity:[[plan objectForKey:@"density"] doubleValue] withAntiGravity:[[plan objectForKey:@"antiGravity"] boolValue] withMoon:[[plan objectForKey:@"hasMoon"] boolValue] withMoonAngle:[[plan objectForKey:@"moonAngle"] doubleValue]];
        planet.position = ccp([[plan objectForKey:@"x"] doubleValue],[[plan objectForKey:@"y"] doubleValue]);
        planet.scale = [[plan objectForKey:@"radius"] doubleValue] / 70.0;
        planet.radius = [[plan objectForKey:@"radius"] doubleValue];
        planet.density = [[plan objectForKey:@"density"] doubleValue];
        planet.antiGravity = [[plan objectForKey:@"antiGravity"] boolValue];
        planet.hasMoon = [[plan objectForKey:@"hasMoon"] boolValue];
        planet.moonAngle = [[plan objectForKey:@"moonAngle"] doubleValue];
        planet.tag = 2;
        [planets addObject:planet];
        [gameLayer addChild:planet z:2];
    }
}

-(void) play
{
    
    
    if(!shipFlying)
    {
        [self resetMission];
        startFlying = YES;
        ship.visible = YES;
        for(CCSprite *beacon in thePath)
        {
            if(beacon.tag != 5)
                beacon.visible = NO;
        }
    }else{
        [self resetMission];        
    }
}

- (void) backToSolveMenu
{
    [self.playFlightSchoolMissionViewController dismissModalViewControllerAnimated:YES];
}

-(void) ccTouchesBegan:(NSSet *)touches withEvent:(UIEvent *)event
{
    UITouch *touch = [touches anyObject];
    CGPoint tempTouch = [touch locationInView:[touch view]];
    tempTouch = [[CCDirector sharedDirector] convertToGL:tempTouch];
    tempTouch = [gameLayer convertToNodeSpace:tempTouch];
    for (CCSprite *sprite in wells) {
        if(sprite.visible && pow(tempTouch.x-sprite.position.x,2) + pow(tempTouch.y - sprite.position.y,2) < pow(32.0,2))
        {
            [self resetMission];
            selectedSprite = sprite;
            panning = NO;
            panningTouch = touch;
            [_controller disable];
            [_controller._touches removeObject:touch];
            break;
        }
    }
    if(movingPoint < 0 && selectedSprite == nil)
    {
        int putAt = -1;
        [self resetMission];
        float start_distance =  sqrtf(powf(start.position.x - tempTouch.x,2) + powf(start.position.y - tempTouch.y,2));
        float probe_distance =  sqrtf(powf(probe.position.x - tempTouch.x,2) + powf(probe.position.y - tempTouch.y,2));
        if(start_distance < point_threshold || probe_distance < point_threshold)
        {
            probe.visible = YES;
            probe.position = ccp(tempTouch.x, tempTouch.y);
            probeActive = NO;
            placingProbe = YES;
            panning = NO;
            panningTouch = touch;
            lastProbeMoveSpeed = 0;
            [_controller disable];
            [_controller._touches removeObject:touch];
        }else{
            int betweenIndex = 0;
            double closestPoint = point_threshold;
            while ([thePath count] > betweenIndex + 1) {
                CCSprite *sprite1 = [thePath objectAtIndex:betweenIndex];
                CGPoint p2 = sprite1.position;
                float distance =  sqrtf(powf(p2.x - tempTouch.x,2) + powf(p2.y - tempTouch.y,2));
                if(distance < closestPoint && betweenIndex != 0)
                {
                    movingPoint = betweenIndex;
                    closestPoint = distance;
                }
                betweenIndex++;
            }
            if(movingPoint < 0)
            {
                
                int lastPointIndex = 0;
                for(int pathIndex = 1;pathIndex < [thePath count];pathIndex++)
                {
                    if(putAt >= 0)
                        break;
                    CCSprite *sprite1 = [thePath objectAtIndex:pathIndex];
                    double closestPinPoint = point_threshold;
                    for(int pointIndex = lastPointIndex;pointIndex < [pathPoints count];pointIndex++)
                    {
                        lastPointIndex = pointIndex;
                        NSArray *point = [pathPoints objectAtIndex:pointIndex];
                        double x = [[point objectAtIndex:0] doubleValue];
                        double y = [[point objectAtIndex:1] doubleValue];
                        double distance = sqrtf(powf(x - tempTouch.x,2) + powf(y - tempTouch.y,2));
                        if(distance < closestPinPoint)
                        {
                            closestPinPoint = distance;
                            putAt = pathIndex;
                        }
                        double distanceFromSprite = sqrtf(powf(x - sprite1.position.x,2) + powf(y - sprite1.position.y,2));
                        if(distanceFromSprite < 10)
                            break;
                    }
                }
            }
            if(movingPoint < 0 && putAt >=0)
            {
                newPoint = YES;
                CCSprite *beacon = [CCSprite spriteWithSpriteFrameName:@"beacon_1.png"];
                id beaconAction = [CCRepeatForever actionWithAction:[CCAnimate actionWithSpriteSequence:@"beacon_%d.png" numFrames:2 delay:0.25 restoreOriginalFrame:NO]];
                [beacon runAction:beaconAction];
                beacon.position = ccp(tempTouch.x,tempTouch.y);
                [pathLayer addChild:beacon z:50];
                [thePath insertObject:beacon atIndex:putAt];
                movingPoint = putAt;
                panning = NO;
                panningTouch = touch;
                [_controller disable];
                [_controller._touches removeObject:touch];
            }else if(movingPoint >= 0)
            {
                newPoint = NO;
                CCSprite *beacon = [thePath objectAtIndex:movingPoint];
                beacon.position = ccp(tempTouch.x,tempTouch.y);
                panning = NO;
                panningTouch = touch;
                [_controller disable];
                [_controller._touches removeObject:touch];
            }
        }
    }
}

-(void) ccTouchesMoved:(NSSet *)touches withEvent:(UIEvent *)event
{
    UITouch *touch = [touches anyObject];
    if(!panning && [touch isEqual:panningTouch])
    {
        CGPoint tempTouch = [touch locationInView:[touch view]];
        tempTouch = [[CCDirector sharedDirector] convertToGL:tempTouch];
        tempTouch = [gameLayer convertToNodeSpace:tempTouch];
        
        if (selectedSprite) {
            selectedSprite.position = tempTouch;
            if(selectedSprite.position.y > 724)
                selectedSprite.position = CGPointMake(selectedSprite.position.x, 724);
            if(selectedSprite.position.y < 0)
                selectedSprite.position = CGPointMake(selectedSprite.position.x, 0);
            if(selectedSprite.position.x > 1024)
                selectedSprite.position = CGPointMake(1024, selectedSprite.position.y);
            if(selectedSprite.position.x < 0)
                selectedSprite.position = CGPointMake(0, selectedSprite.position.y);
        }else if(placingProbe)
        {
            previousProbeX = probe.position.x;
            previousProbeY = probe.position.y;
            probe.position = ccp(tempTouch.x,tempTouch.y);
            
            if(probe.position.y > 724)
                probe.position = CGPointMake(probe.position.x, 724);
            if(probe.position.y < 0)
                probe.position = CGPointMake(probe.position.x, 0);
            if(probe.position.x > 1024)
                probe.position = CGPointMake(1024, probe.position.y);
            if(probe.position.x < 0)
                probe.position = CGPointMake(0, probe.position.y);
            NSDate *now = [[NSDate alloc] init];
            if(lastProbeMoveDate != nil)
            {
                NSTimeInterval timeDifference = [now timeIntervalSinceDate:lastProbeMoveDate];
                if(timeDifference > 0)
                {
                    const float lambda = 0.8f; // the closer to 1 the higher weight to the next touch
                    
                    probeSpeed = (1.0 - lambda) * lastProbeMoveSpeed + lambda * (sqrt(pow(probe.position.x - previousProbeX, 2) + pow(probe.position.y - previousProbeY, 2)) / timeDifference);
                    lastProbeMoveSpeed = probeSpeed;
                }
            }
            lastProbeMoveDate = [now retain];
            [now release];
        }else{
            CCSprite *beacon = [thePath objectAtIndex:movingPoint];
            beacon.position = ccp(tempTouch.x,tempTouch.y);
            
            if(beacon.position.y > 724)
                beacon.position = CGPointMake(beacon.position.x, 724);
            if(beacon.position.y < 0)
                beacon.position = CGPointMake(beacon.position.x, 0);
            if(beacon.position.x > 1024)
                beacon.position = CGPointMake(1024, beacon.position.y);
            if(beacon.position.x < 0)
                beacon.position = CGPointMake(0, beacon.position.y);
        }
    }
}

-(void) ccTouchesEnded:(NSSet *)touches withEvent:(UIEvent *)event
{
    
    UITouch *touch = [touches anyObject];
    
    if(!panning && [touch isEqual:panningTouch])
    {
        if(placingProbe)
        {
            if(probeSpeed < 20.0)
                probeSpeed = 0.0;
            if(probeSpeed > 700.0)
                probeSpeed = 700.0;
            placingProbe = NO;
            probeActive = YES;
        }else{
            if(touch.tapCount == 2 && movingPoint > 0 && movingPoint != ([thePath count] - 1)) {
                
                CCSprite *beacon = [thePath objectAtIndex:movingPoint];
                [thePath removeObjectAtIndex:movingPoint];
                [pathLayer removeChild:beacon cleanup:YES];
            }
            movingPoint = -1;
        }
        [_controller enableWithTouchPriority:0 swallowsTouches:NO];
        panning = YES;
        selectedSprite = nil;
    }
    [_controller._touches removeAllObjects];
}
-(void) resetMission {
    shipFlightDuration = 0.0;
    shipOutOfFuel = NO;
    outOfFuel = NO;
    fuel_cost = maxFuel - total_fuel;
    travelTime = 0.0;
    [shipPath removeAllObjects];
    [self.playFlightSchoolMissionViewController.fuelIndicator setProgress:total_fuel / maxFuel animated:YES];
    ship.position = start.position;
    ship.visible = NO;
    shipFlying = NO;
    [self resetAstronauts];
    [self resetItems];
    for(CCPlanet *planet in planets)
    {
        planet.moonAngle = planet.startingMoonAngle;
    }
    for(CCSprite *beacon in thePath)
    {
        beacon.visible = YES;
    }
}
-(void) shipCrashed {
    [TestFlight passCheckpoint:[NSString stringWithFormat:@"Crashed Flight School Mission:%d",missionId]];
    [self resetMission];
    UIView* view = [[CCDirector sharedDirector] openGLView];
    
    UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Mission Failure" message: @"You crashed!" delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
    [view addSubview: myAlertView];
    [myAlertView show];
    [myAlertView release];
}
-(void) shipRanOutOfFuel {
    shipOutOfFuel = YES;
    ship.position = CGPointMake(previousShipX, previousShipY);
    previousShipX = previousPreviousShipX;
    previousShipY = previousPreviousShipY;
    
    [self.playFlightSchoolMissionViewController.fuelIndicator setProgress:0.0 animated:YES];
}
-(void) shipDoneFlying {
    bool savedAstronauts = YES;
    for (CCSprite *astronaut in astronauts)
    {
        if(astronaut.visible == YES)
        {
            savedAstronauts = NO;
        }
    }
    if(savedAstronauts)
    {
        [TestFlight passCheckpoint:[NSString stringWithFormat:@"Completed Flight School Mission:%d",missionId]];
        
        
        
        NSArray *filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        NSString *fileDirectory = [[NSString alloc] initWithFormat:@"%@",[filePaths objectAtIndex:0]];
        NSString *fileName = [[NSString alloc] initWithFormat:@"%@/FlightSchool.plist", fileDirectory];
        NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:fileName];
        NSMutableDictionary *completedMissions = [[NSMutableDictionary alloc] initWithDictionary:[[dict objectForKey:@"completedMissions"] mutableCopy]];
        
        [completedMissions setObject:[NSNumber numberWithBool:YES] forKey:[NSString stringWithFormat:@"%d",missionId]];
        [dict setObject:completedMissions forKey:@"completedMissions"];
        [dict setObject:[NSNumber numberWithInt:missionId] forKey:@"justCompletedMission"];
        if(![dict writeToFile:fileName atomically:YES])
            NSLog(@"failed to write plist");
        [fileName release];
        [fileDirectory release];
        [completedMissions release];
    
        UIView* view = [[CCDirector sharedDirector] openGLView];
        
        UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Mission Complete" message:@"Press Back to move on\nto the next Mission." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
        [view addSubview: myAlertView];
        [myAlertView show];
        [myAlertView release];
    }else{
        [TestFlight passCheckpoint:[NSString stringWithFormat:@"Didn't Save Astronauts Flight School Mission:%d",missionId]];
        UIView* view = [[CCDirector sharedDirector] openGLView];
        
        UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Mission Failure" message: @"You didn't save all of the astronauts!" delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
        [view addSubview: myAlertView];
        [myAlertView show];
        [myAlertView release];
    }
    [self resetMission];
}
- (void)onExit {
    
    [_controller disable];
    [_controller release];
    [astronauts release];
    [items release];
    [wells release];
    [planets release];
    [astronautsData release];
    [planetsData release];
    [itemsData release];
    [wellsData release];
    [thePath release];
    [pathPoints release];
    
    [pathLayer removeAllChildrenWithCleanup:YES];
    [pathLayer unscheduleAllSelectors];
    
    [self removeAllChildrenWithCleanup:YES];
    [self stopAllActions];
    [self unscheduleAllSelectors];
    [[CCTouchDispatcher sharedDispatcher] removeAllDelegates];
    [[CCTextureCache sharedTextureCache] removeAllTextures];
    
    [super onExit];
}

// on "dealloc" you need to release all your retained objects
- (void) dealloc
{
    [lastProbeMoveDate release];
	[super dealloc];
}
@end