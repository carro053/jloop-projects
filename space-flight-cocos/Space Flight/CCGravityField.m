#import "CCGravityField.h"
#import "CCPlanet.h"
#import "Constants.h"

@implementation CCGravityField

@synthesize parentScene;

+(CCGravityField *) layerWithParent:(EditMissionScene *)theParent
{
	CCGravityField *layer = [CCGravityField node];
	layer.parentScene = theParent;
	return layer;
}

-(id) init {
    if((self=[super init])){
        
    }
    return self;
}

-(void) draw {
    if(parentScene.gravityField)
    {
        for(double x = 32.0;x < 1024.0;x += 32.0)
        {
            for(double y = 32.0;y < 768.0;y += 32.0)
            {
                bool hasPlanets = NO;
                
                for (CCPlanet *planet in parentScene.planets) {
                    if(planet.visible)
                        hasPlanets = YES;
                }
                for (CCSprite *well in parentScene.wells) {
                    if(well.visible)
                        hasPlanets = YES;
                }
                        
                if(hasPlanets)
                {
                    double gx = 0.0;
                    double gy = 0.0;
                    for (CCPlanet *planet in parentScene.planets) {
                        if(planet.visible)
                        {
                            double planetX = planet.position.x;
                            double planetY = planet.position.y;
                            double planetRadius = planet.radius;
                            double planetDensity = planet.density;
                            double planetMass = planetDensity * 4 / 3 * M_PI * pow(planetRadius, 3);
                            double gravity = gConstant * planetMass * shipMass / pow(sqrt(pow(x - planetX,2) + pow(y - planetY,2)) * gDistanceConstant,2);
                            if(planet.antiGravity)
                                gravity = gravity * -1.0;
                            double g_x = (x - planetX) / sqrt(pow(x - planetX, 2) + pow(y - planetY, 2));
                            double g_y = (y - planetY) / sqrt(pow(x - planetX, 2) + pow(y - planetY, 2));
                            gx -= g_x * gravity;
                            gy -= g_y * gravity;
                            if (planet.hasMoon) {
                                double currentMoonAngle = planet.startingMoonAngle;
                                double moonX = planet.position.x + cos(currentMoonAngle) * planet.radius * planet.xOrbit;
                                double moonY = planet.position.y + sin(currentMoonAngle) * planet.radius * planet.yOrbit;
                                double moonRadius = planet.radius * planet.moonRadius;
                                double moonDensity = moon_density;
                                double moonMass = moonDensity * 4 / 3 * M_PI * pow(moonRadius, 3);
                                double moonGravity = gConstant * moonMass * shipMass / pow(sqrt(pow(x - moonX,2) + pow(y - moonY,2)) * gDistanceConstant,2);
                                double mg_x = (x - moonX) / sqrt(pow(x - moonX, 2) + pow(y - moonY, 2));
                                double mg_y = (y - moonY) / sqrt(pow(x - moonX, 2) + pow(y - moonY, 2));
                                gx -= mg_x * moonGravity;
                                gy -= mg_y * moonGravity;
                            }
                        }
                    }
                    
                    for (CCSprite *well in parentScene.wells) {
                        if(well.visible)
                        {
                            double wellX = well.position.x;
                            double wellY = well.position.y;
                            int wellPower = well.tag;
                            double gravity = gConstant * wellPower * shipMass / pow(sqrt(pow(x - wellX,2) + pow(y - wellY,2)) * gDistanceConstant,2);
                            double g_x = (x - wellX) / sqrt(pow(x - wellX, 2) + pow(y - wellY, 2));
                            double g_y = (y - wellY) / sqrt(pow(x - wellX, 2) + pow(y - wellY, 2));
                            gx -= g_x * gravity;
                            gy -= g_y * gravity;
                        }
                    }
                    double angle = atan(gy / gx);
                    if(gx < 0) angle += M_PI;
                    double totalGravity = sqrt(pow(gx,2) + pow(gy,2));
                    double energyMeter = MIN(totalGravity / gConstant / 0.50,2);
                    if(energyMeter > 1)
                    {
                        glColor4f(1, 2 - energyMeter, 0, 1.0);
                    }else{
                        glColor4f(energyMeter, 1, 0, 1.0);
                    }
                    if ([[UIScreen mainScreen] respondsToSelector:@selector(scale)] && [[UIScreen mainScreen] scale] == 2)
                    {
                        glLineWidth(2.0f);
                    }else{
                        glLineWidth(1.0f);
                    }
                    double x_diff = 13 * cos(angle) * energyMeter;
                    double y_diff = 13 * sin(angle) * energyMeter;
                    float min_diff = 1.0;
                    if(x_diff < min_diff && x_diff > -min_diff && y_diff < min_diff && y_diff > -min_diff)
                    {
                        ccDrawPoint(CGPointMake(x, y));
                    }else{
                        if(x_diff < 1.0 && x_diff >= 0.0)
                            x_diff = 1.0;
                        if(x_diff > -1.0 && x_diff < 0.0)
                            x_diff = -1.0;
                        if(y_diff < 1.0 && y_diff >= 0.0)
                            y_diff = 1.0;
                        if(y_diff > -1.0 && y_diff < 0.0)
                            y_diff = -1.0;
                        
                        ccDrawLine(CGPointMake(x, y),CGPointMake(x + x_diff, y + y_diff));
                    }
                }else{
                    glColor4f(0, 1, 0, 1.0);
                    glLineWidth(1.0f);
                    ccDrawPoint(CGPointMake(x, y));
                }
                
                
            }
        }
    }
    [super draw];
}
- (void) dealloc
{
	[super dealloc];
}

@end