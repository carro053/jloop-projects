//
//  HelloWorldLayer.m
//  LineDrawing
//
//  Created by Michael Stratford on 4/6/12.
//  Copyright JLOOP 2012. All rights reserved.
//


// Import the interfaces
#import "ViewMissionSolutionScene.h"
#import "MoveArray.m"
#import "CCPlanet.h"
#import "CCItem.h"
#import "CCAnimate+SequenceLoader.h"
#import "PathLayer.h"
#import "SBJson.h"
#import "Constants.h"
int puzzleId;
int solutionId;

UIBezierPath *bezPath;

NSMutableArray *astronautsData;
NSMutableArray *itemsData;
NSMutableArray *wellsData;
NSMutableArray *planetsData;
int movingPoint;
BOOL newPoint;
BOOL startFlying;
bool startup;
PathLayer *pathLayer;
bool shipOutOfFuel;

CCSprite *fuelTank;
CCSprite *space;

CCSprite *start;
CCSprite *end;
CCMenu *moveMenu;
CCMenu *adjustMenu;

double most_fuel;
double fastest_time;


// HelloWorldLayer implementation
@implementation ViewMissionSolutionScene

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

@synthesize viewMissionSolutionViewController = _viewMissionSolutionViewController;

+(CCScene *) sceneWithId:(int)theId andSolutionId:(int)theSecondId viewController:(ViewMissionSolutionViewController *)viewController
{
    puzzleId = theId;
    solutionId = theSecondId;
    NSLog(@"%d - %d",theSecondId,solutionId);
	// 'scene' is an autorelease object.
	CCScene *scene = [CCScene node];
	
	// 'layer' is an autorelease object.
	ViewMissionSolutionScene *layer = [ViewMissionSolutionScene node];
    layer.viewMissionSolutionViewController = viewController;
	
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
        [TestFlight passCheckpoint:[NSString stringWithFormat:@"View Solution for Online Mission:%d",puzzleId]];
		astronauts = [[NSMutableArray alloc] init];
		items = [[NSMutableArray alloc] init];
		wells = [[NSMutableArray alloc] init];
		planets = [[NSMutableArray alloc] init];
		thePath = [[NSMutableArray alloc] init];
		shipPath = [[NSMutableArray alloc] init];
		pathPoints = [[NSMutableArray alloc] init];
        NSDictionary *puzzle = [self getOnlinePuzzleWithSolution];
        [self performSelectorOnMainThread:@selector(loadScene:) withObject:puzzle waitUntilDone:YES];
	}
	return self;
}

-(void) loadScene:(NSDictionary *)puzzle {
    
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
    [gameLayer addChild:pathLayer z:0];
    
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
    
    NSArray *startData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:240.0],[NSNumber numberWithDouble:160.0],nil];
    NSArray *endData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:784.0],[NSNumber numberWithDouble:558.0],nil];
    most_fuel = 0.0;
    fastest_time = 0.0;
    astronautsData = [[NSMutableArray alloc] initWithArray:[puzzle objectForKey:@"astronauts"]];
    planetsData = [[NSMutableArray alloc] initWithArray:[puzzle objectForKey:@"planets"]];
    itemsData = [[NSMutableArray alloc] initWithArray:[puzzle objectForKey:@"items"]];
    wellsData = [[NSMutableArray alloc] initWithArray:[puzzle objectForKey:@"wells"]];
    total_fuel = [[puzzle objectForKey:@"total_fuel"] intValue];
    startData = [puzzle objectForKey:@"startData"];
    endData = [puzzle objectForKey:@"endData"];
    most_fuel = [[puzzle objectForKey:@"most_fuel"] doubleValue];
    fastest_time = [[puzzle objectForKey:@"fastest_time"] doubleValue];
    NSArray *wayPoints = [NSArray arrayWithArray:[puzzle objectForKey:@"way_points"]];
    
    [self setupAstronauts];
    [self setupItems];
    [self setupWells];
    [self setupPlanets];
    
    
    start = [CCSprite spriteWithSpriteFrameName:@"space_station.png"];
    start.position = ccp([[startData objectAtIndex:0] doubleValue],[[startData objectAtIndex:1] doubleValue]);
    start.scale = 2.0;
    start.tag = 5;
    [gameLayer addChild:start z:50];
    [thePath addObject:start];
    
    for(NSDictionary *wayPoint in wayPoints)
    {
        CCSprite *beacon = [CCSprite spriteWithSpriteFrameName:@"beacon_1.png"];
        id beaconAction = [CCRepeatForever actionWithAction:[CCAnimate actionWithSpriteSequence:@"beacon_%d.png" numFrames:2 delay:0.25 restoreOriginalFrame:NO]];
        [beacon runAction:beaconAction];
        beacon.position = ccp([[wayPoint objectForKey:@"x"] doubleValue],[[wayPoint objectForKey:@"y"] doubleValue]);
        [pathLayer addChild:beacon z:50];
        [thePath addObject:beacon];
    }
    
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
    
    fuel_cost = maxFuel - total_fuel;
    self.viewMissionSolutionViewController.fuelIndicator.progress = 0;
    [self.viewMissionSolutionViewController.fuelIndicator setProgress:total_fuel / maxFuel animated:YES];
    [self schedule:@selector(tick:) interval: 1/60.0f];
}
- (void) onEnterTransitionDidFinish {
    [self.viewMissionSolutionViewController.fuelIndicator setProgress:((total_fuel) / maxFuel) animated:YES];
    [self play];
    [super onEnterTransitionDidFinish];
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

- (NSDictionary *) getOnlinePuzzleWithSolution
{
	id response = [self objectWithUrl:[NSURL URLWithString:[NSString stringWithFormat:@"http://dev.gravitationsapp.com/missions/getMissionWithSolution/%d/%d",puzzleId,solutionId]]];
	NSDictionary *feed = (NSDictionary *)response;
	return feed;
}


-(void) ccTouchesBegan:(NSSet *)touches withEvent:(UIEvent *)event
{
    
}

-(void) ccTouchesMoved:(NSSet *)touches withEvent:(UIEvent *)event
{
    
}

-(void) ccTouchesEnded:(NSSet *)touches withEvent:(UIEvent *)event
{
    
}

-(void) toggleLine {
    colorLine = !colorLine;
}
-(void) draw {    
    if(startup)
    {
        [_controller zoomOutOnPoint:CGPointMake(512,362) duration:0];
        startup = NO;
    }
    [super draw];
}
-(void) setupAstronauts {
    for(NSDictionary *astro in astronautsData)
    {
        CCSprite *astronaut = [CCSprite spriteWithSpriteFrameName:@"astronaut.png"];
        astronaut.position = ccp([[astro objectForKey:@"x"] doubleValue],[[astro objectForKey:@"y"] doubleValue]);
        [astronaut setRotation:arc4random() % 360];
        astronaut.tag = 1;
        [astronauts addObject:astronaut];
        [gameLayer addChild:astronaut z:2];
    }
}

-(void) setupItems {
    for(NSDictionary *theItem in itemsData)
    {
        CCItem *item = [CCItem spriteWithSpriteFrameName:@"fuel.png" andType:@"Fuel"];
        item.position = ccp([[theItem objectForKey:@"x"] doubleValue],[[theItem objectForKey:@"y"] doubleValue]);
        [item setRotation:arc4random() % 360];
        [items addObject:item];
        [gameLayer addChild:item z:2];
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
        [gameLayer addChild:planet z:1];
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
-(void) tick: (ccTime) dt
{
    [self updateShip:dt];
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
                [ship setRotation:(ship.rotation + 360.0 * dt)];
                
                if(gx < 0)
                {
                    new_v_x += sqrt(gx * -1.0 / shipMass) * travel_time;
                }else{
                    new_v_x -= sqrt(gx * 1.0 / shipMass) * travel_time;
                }
                if(gy < 0)
                {
                    new_v_y += sqrt(gy * -1.0 / shipMass) * travel_time;
                }else{
                    new_v_y -= sqrt(gy * 1.0 / shipMass) * travel_time;
                }
                
                double new_x = new_v_x * travel_time;
                double new_y = new_v_y * travel_time;
                shipSpeed = sqrt(pow(new_v_x,2) + pow(new_v_y,2));
                previousShipX = ship.position.x;
                previousShipY = ship.position.y;
                ship.position = ccp(ship.position.x + new_x,ship.position.y + new_y);
            }else{
                [self resetMission];
                UIView* view = [[CCDirector sharedDirector] openGLView];
                UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Mission Failure" message: @"You have ran out of fuel and drifted off into the abyss!" delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
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
                    
                    [self.viewMissionSolutionViewController.fuelIndicator setProgress:(maxFuel - [[shipPoint objectForKey:@"total_fuel_used"] doubleValue]) / maxFuel animated:YES];
                    travelTime = [[shipPoint objectForKey:@"total_travel_time"] doubleValue];
                    fuel_cost = [[shipPoint objectForKey:@"total_fuel_used"] doubleValue];
                    shipUpdated = YES;
                    break;
                }
            }
            if(shipUpdated)
            {
                if(1 == 2 && fuel_cost > maxFuel)
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

- (void) backToOnlinePuzzle
{
	[self.viewMissionSolutionViewController dismissModalViewControllerAnimated:YES];
}

-(void) resetMission {
    shipFlightDuration = 0.0;
    shipOutOfFuel = NO;
    outOfFuel = NO;
    fuel_cost = maxFuel - total_fuel;
    travelTime = 0.0;
    [shipPath removeAllObjects];
    [self.viewMissionSolutionViewController.fuelIndicator setProgress:total_fuel / maxFuel animated:YES];
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
    [self resetMission];
    UIView* view = [[CCDirector sharedDirector] openGLView];
    
    UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Failure!" message: @"You crashed!" delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
    [view addSubview: myAlertView];
    [myAlertView show];
    [myAlertView release];
}
-(void) shipRanOutOfFuel {
    [self resetMission];
    UIView* view = [[CCDirector sharedDirector] openGLView];
    
    UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle: @"Failure!" message: @"You ran out of fuel." delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
    [view addSubview: myAlertView];
    [myAlertView show];
    [myAlertView release];
}
-(void) shipDoneFlying {
    [self resetMission];
    [self play];
}


- (void)onExit {
    [_controller disable];
    [_controller release];
    [astronauts release];
    [items release];
    [wells release];
    [planets release];
    [astronautsData release];
    [itemsData release];
    [wellsData release];
    [planetsData release];
    [thePath release];
    [shipPath release];
    [pathPoints release];
    
    [self removeAllChildrenWithCleanup:YES];
    [self stopAllActions];
    [self unscheduleAllSelectors];
    [[CCTouchDispatcher sharedDispatcher] removeAllDelegates];
    [[CCTextureCache sharedTextureCache] removeAllTextures];
    [super onExit];
}

- (void) dealloc
{
	[super dealloc];
}
@end
