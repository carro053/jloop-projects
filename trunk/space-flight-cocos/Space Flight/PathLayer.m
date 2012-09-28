#import "PathLayer.h"
#import "CCPlanet.h"
#import "CCItem.h"
#import "Constants.h"

double currentSpeed;
double total_travel_time;
double total_fuel_used;

bool drawing;


@implementation PathLayer

@synthesize parentScene;

+(PathLayer *) layerWithParent:(PlayMissionScene *)theParent
{
	PathLayer *layer = [PathLayer node];
	layer.parentScene = theParent;
	return layer;
}

-(id) init {
    if((self=[super init])){
    }
    return self;
}

-(void) draw {
    if (!parentScene.shipFlying) {
        total_travel_time = 0.0;
        total_fuel_used = parentScene.fuel_cost;
        NSMutableArray *pathArray = [[NSMutableArray alloc] init];
        for(CCSprite *sprite in parentScene.thePath)
        {
            NSValue *point = [NSValue valueWithCGPoint:sprite.position];
            [pathArray addObject:point];
        }
        while([pathArray count] < 4)
            [pathArray addObject:[pathArray objectAtIndex:([pathArray count]-1)]];
        
        int sz = [pathArray count];
        Point2 d[sz];
        int i = 0;
        for (NSValue *val in pathArray) {
            CGPoint pnt = [val CGPointValue];
            d[i].x = pnt.x;
            d[i].y = pnt.y;
            i++;
        }
        double	error = 50;
        currentSpeed = minSpeed;
        [parentScene.pathPoints removeAllObjects];
        if (sz > 0)
            [self FitCurvePoints:d numberOf:sz theError:error];
        [pathArray release];
    }
    if(parentScene.startFlying == YES)
    {
        parentScene.shipFlying = YES;
        parentScene.shipFlightDuration = 0.0;
        parentScene.startFlying = NO;
    }
    [super draw];
}

-(void) DrawBezierCurveInt:(int)n Curve:(BezierCurve)curve {
    
    double start_x = [self BezierPointT:0.0 Start:curve[0].x Control1:curve[1].x Control2:curve[2].x End:curve[3].x];
    double start_y = [self BezierPointT:0.0 Start:curve[0].y Control1:curve[1].y Control2:curve[2].y End:curve[3].y];
    double middle_x = [self BezierPointT:0.5 Start:curve[0].x Control1:curve[1].x Control2:curve[2].x End:curve[3].x];
    double middle_y = [self BezierPointT:0.5 Start:curve[0].y Control1:curve[1].y Control2:curve[2].y End:curve[3].y];
    double end_x = [self BezierPointT:1.0 Start:curve[0].x Control1:curve[1].x Control2:curve[2].x End:curve[3].x];
    double end_y = [self BezierPointT:1.0 Start:curve[0].y Control1:curve[1].y Control2:curve[2].y End:curve[3].y];
    double curve_length = sqrt(pow(start_x - middle_x, 2) + pow(start_y - middle_y, 2)) + sqrt(pow(middle_x - end_x, 2) + pow(middle_y - end_y, 2));
     
    double t;
    int i;
    int steps;
    CGPoint dot;
    CGPoint previous_dot;
    CGPoint previous_previous_dot;
    double length = 0.0;
    
    if(parentScene.startFlying)
    {
        steps = ceil(curve_length * 2);
    }else{
        steps = ceil(curve_length / 4);
    }
    for (i = 0; i <= steps; i++) {
        double this_length;
        t = (double) i / (double) steps;
        dot.x = [self BezierPointT:t Start:curve[0].x Control1:curve[1].x Control2:curve[2].x End:curve[3].x];
        dot.y = [self BezierPointT:t Start:curve[0].y Control1:curve[1].y Control2:curve[2].y End:curve[3].y];
        if (i > 1) {
            
            double x_diff = dot.x - previous_dot.x;
            double y_diff = dot.y - previous_dot.y;
            length += sqrt(x_diff * x_diff + y_diff * y_diff);
            this_length = sqrt(x_diff * x_diff + y_diff * y_diff);
            if((previous_dot.x - previous_previous_dot.x != 0.0 || previous_dot.y - previous_previous_dot.y != 0.0) && (dot.x - previous_dot.x != 0.0 || dot.y - previous_dot.y != 0.0))
            {
                NSArray *point = [[NSArray alloc] initWithObjects:[NSNumber numberWithDouble:previous_dot.x],[NSNumber numberWithDouble:previous_dot.y], nil];
                [parentScene.pathPoints addObject:point];
                [point release];
                double travel_time = this_length / currentSpeed;
                total_travel_time += travel_time;
                double u_x = (previous_dot.x - previous_previous_dot.x) / sqrt(pow(previous_dot.x - previous_previous_dot.x, 2) + pow(previous_dot.y - previous_previous_dot.y, 2));
                double u_y = (previous_dot.y - previous_previous_dot.y) / sqrt(pow(previous_dot.x - previous_previous_dot.x, 2) + pow(previous_dot.y - previous_previous_dot.y, 2));
                double new_v_x = u_x * currentSpeed;
                double new_v_y = u_y * currentSpeed;
                double gx = 0.0;
                double gy = 0.0;
                for (CCPlanet *planet in parentScene.planets) {
                    double planetX = planet.position.x;
                    double planetY = planet.position.y;
                    double planetRadius = planet.radius;
                    double planetDensity = planet.density;
                    double planetMass = planetDensity * 4 / 3 * M_PI * pow(planetRadius, 3);
                    double gravity = gConstant * planetMass * shipMass / pow(sqrt(pow(previous_dot.x - planetX,2) + pow(previous_dot.y - planetY,2)) * gDistanceConstant,2);
                    if(planet.antiGravity)
                        gravity = gravity * -1.0;
                    double g_x = (previous_dot.x - planetX) / sqrt(pow(previous_dot.x - planetX, 2) + pow(previous_dot.y - planetY, 2));
                    double g_y = (previous_dot.y - planetY) / sqrt(pow(previous_dot.x - planetX, 2) + pow(previous_dot.y - planetY, 2));
                    gx += g_x * gravity;
                    gy += g_y * gravity;
                    if (planet.hasMoon) {
                        double currentMoonAngle = planet.startingMoonAngle + M_PI / planet.radius * (planet.density + 0.012) / 0.03 / 2.0 * 60.0 * total_travel_time;
                        double moonX = planet.position.x + cos(currentMoonAngle) * planet.radius * planet.xOrbit;
                        double moonY = planet.position.y + sin(currentMoonAngle) * planet.radius * planet.yOrbit;
                        double moonRadius = planet.radius * planet.moonRadius;
                        double moonDensity = moon_density;
                        double moonMass = moonDensity * 4 / 3 * M_PI * pow(moonRadius, 3);
                        double moonGravity = gConstant * moonMass * shipMass / pow(sqrt(pow(previous_dot.x - moonX,2) + pow(previous_dot.y - moonY,2)) * gDistanceConstant,2);
                        double mg_x = (previous_dot.x - moonX) / sqrt(pow(previous_dot.x - moonX, 2) + pow(previous_dot.y - moonY, 2));
                        double mg_y = (previous_dot.y - moonY) / sqrt(pow(previous_dot.x - moonX, 2) + pow(previous_dot.y - moonY, 2));
                        gx += mg_x * moonGravity;
                        gy += mg_y * moonGravity;
                    }
                    
                }
                for (CCSprite *well in parentScene.wells) {
                    double wellX = well.position.x;
                    double wellY = well.position.y;
                    int wellPower = well.tag;
                    double gravity = gConstant * wellPower * shipMass / pow(sqrt(pow(previous_dot.x - wellX,2) + pow(previous_dot.y - wellY,2)) * gDistanceConstant,2);
                    double g_x = (previous_dot.x - wellX) / sqrt(pow(previous_dot.x - wellX, 2) + pow(previous_dot.y - wellY, 2));
                    double g_y = (previous_dot.y - wellY) / sqrt(pow(previous_dot.x - wellX, 2) + pow(previous_dot.y - wellY, 2));
                    gx += g_x * gravity;
                    gy += g_y * gravity;
                }
                new_v_x -= gx / shipMass * travel_time;
                new_v_y -= gy / shipMass * travel_time;
                
                //if(parentScene.startFlying == YES)
                    //NSLog(@"gx:%f gy:%f new_v_x:%f new_v_y:%f travel_time:%f Speed:%f",gx,gy,new_v_x,new_v_y,travel_time,currentSpeed);
                double newSpeed = sqrt(pow(new_v_x,2) + pow(new_v_y,2));
                double n_x = new_v_x / sqrt(pow(new_v_x, 2) + pow(new_v_y, 2));
                double n_y = new_v_y / sqrt(pow(new_v_x, 2) + pow(new_v_y, 2));
                double v_x = (dot.x - previous_dot.x) / sqrt(pow(dot.x - previous_dot.x, 2) + pow(dot.y - previous_dot.y, 2));
                double v_y = (dot.y - previous_dot.y) / sqrt(pow(dot.x - previous_dot.x, 2) + pow(dot.y - previous_dot.y, 2));
                double scalar = newSpeed * (n_x * v_x + n_y * v_y);
                double w = sqrt(pow(newSpeed,2) - pow(scalar,2));
                double fuelSpent = 0;
                double min_speed_thrust = 0.0;
                double on_course_thrust = 0.0;
                if(scalar < minSpeed)
                {
                    min_speed_thrust = shipMass * (minSpeed - scalar);
                    fuelSpent += min_speed_thrust;
                    currentSpeed = minSpeed;
                }else {
                    currentSpeed = scalar;
                }
                if(w > 0)
                {
                    on_course_thrust = shipMass * w;
                    fuelSpent += on_course_thrust;
                }
                fuelSpent /= fuelPower;
                total_fuel_used += fuelSpent;
                if(parentScene.startFlying == YES)
                {
                    //NSLog(@"x:%f y: %f FUEL:%f TIME:%f TRAVEL:%f SPEED:%f",previous_dot.x,previous_dot.y,total_fuel_used,total_travel_time,travel_time,currentSpeed);
                    for (CCItem *item in parentScene.items)
                    {
                        double separation = sqrt(pow(item.position.x - previous_dot.x,2) + pow(item.position.y - previous_dot.y,2));
                        if(separation < saveThreshold && item.tag != 5)
                        {
                            item.tag = 5;
                            if([item.itemType isEqualToString:@"Fuel"])
                            {
                                total_fuel_used = 0.0;
                            }
                        }
                    }
                    double thrust_angle;
                    if(fuelSpent < 0.01)
                    {
                        thrust_angle = 0.0;
                    }else if(min_speed_thrust == 0.0)
                    {
                        thrust_angle = 90.0;
                    }else{
                        thrust_angle = atan(on_course_thrust/min_speed_thrust) * 180 / M_PI;
                    }
                    double path_angle = -180.0 / M_PI * ccpToAngle(CGPointMake(v_x,v_y));
                    double actual_angle = -180.0 / M_PI * ccpToAngle(CGPointMake(n_x,n_y));
                    if(actual_angle >= path_angle)
                    {
                        thrust_angle = 0 + 180 + thrust_angle;
                    }else{
                        thrust_angle = 0 + 180 - thrust_angle;
                    }
                    NSDictionary *shipPoint = [NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:[NSNumber numberWithDouble:(double)previous_dot.x],[NSNumber numberWithDouble:(double)previous_dot.y],[NSNumber numberWithDouble:path_angle],[NSNumber numberWithDouble:total_fuel_used],[NSNumber numberWithDouble:total_travel_time],[NSNumber numberWithDouble:currentSpeed],[NSNumber numberWithDouble:fuelSpent],[NSNumber numberWithDouble:thrust_angle], nil] forKeys:[NSArray arrayWithObjects:@"x",@"y",@"angle",@"total_fuel_used",@"total_travel_time",@"speed",@"thrust_power",@"thrust_angle",nil]];
                    [parentScene.shipPath addObject:shipPoint];
                }else if(parentScene.shipFlying == NO) {
                    if(parentScene.colorLine)
                    {
                        double energyMeter = MIN(log10(1 + pow(fuelSpent / travel_time, 2)) / log10(14400), 2);
                        if(energyMeter > 1)
                        {
                            energyMeter -= 1;
                            glColor4f(1, 1 - energyMeter, 0, 1.0);
                        }else{
                            glColor4f(energyMeter, 1, 0, 1.0);
                        }
                        if ([[UIScreen mainScreen] respondsToSelector:@selector(scale)] && [[UIScreen mainScreen] scale] == 2)
                        {
                            glLineWidth(2.0f);
                        }else{
                            glLineWidth(1.0f);
                        }
                        ccDrawLine(previous_previous_dot,previous_dot);
                        if(i == steps)
                            ccDrawLine(previous_dot,dot);
                    }else{
                        glColor4f(1, 1, 1, 1.0);
                        CGPoint endPoint = CGPointMake(curve[3].x, curve[3].y);
                        CGPoint control1 = CGPointMake(curve[1].x, curve[1].y);
                        CGPoint control2 = CGPointMake(curve[2].x, curve[2].y);
                        CGPoint startPoint = CGPointMake(curve[0].x, curve[0].y);
                        ccDrawCubicBezier(startPoint, control1, control2, endPoint,(int)100);
                    }
                }
            }
        }
        if(dot.x - previous_dot.x != 0.0 || dot.y - previous_dot.y != 0.0)
        {
            previous_previous_dot = previous_dot;
            previous_dot = dot;
        }
    }
    
}
-(double) BezierPointT:(double)t Start:(double)start Control1:(double)control_1 Control2:(double)control_2 End:(double)end {
    /* Formula from Wikipedia article on Bezier curves. */
    return              start * (1.0 - t) * (1.0 - t)  * (1.0 - t) 
    + 3.0 *  control_1 * (1.0 - t) * (1.0 - t)  * t 
    + 3.0 *  control_2 * (1.0 - t) * t          * t
    +              end * t         * t          * t;
}

-(void) FitCurvePoints:(Point2 *)d numberOf:(int) nPts theError:(double)error
{
    Vector2	tHat1, tHat2;	/*  Unit tangent vectors at endpoints */
    
    tHat1 = ComputeLeftTangent(d, 0);
    tHat2 = ComputeRightTangent(d, nPts - 1);
    [self FitCubicPoints:d First:0 Last:nPts - 1 THat1:tHat1 THat2:tHat2 TheError:error];
}

-(void) FitCubicPoints:(Point2 *)d First:(int)first Last:(int)last THat1:(Vector2)tHat1 THat2:(Vector2)tHat2 TheError:(double)error {
    BezierCurve	bezCurve; /*Control points of fitted Bezier curve*/
    double	*u;		/*  Parameter values for point  */
    double	*uPrime;	/*  Improved parameter values */
    double	maxError;	/*  Maximum fitting error	 */
    int		splitPoint;	/*  Point to split point set at	 */
    int		nPts;		/*  Number of points in subset  */
    double	iterationError; /*Error below which you try iterating  */
    int		maxIterations = 4; /*  Max times to try iterating  */
    Vector2	tHatCenter;   	/* Unit tangent vector at splitPoint */
    int		i;		
    
    iterationError = error * error;
    nPts = last - first + 1;
    
    /*  Use heuristic if region only has two points in it */
    if (nPts == 2) {
	    double dist = V2DistanceBetween2Points(&d[last], &d[first]) / 3.0;
        
		bezCurve = (Point2 *)malloc(4 * sizeof(Point2));
		bezCurve[0] = d[first];
		bezCurve[3] = d[last];
		V2Add(&bezCurve[0], V2Scale(&tHat1, dist), &bezCurve[1]);
		V2Add(&bezCurve[3], V2Scale(&tHat2, dist), &bezCurve[2]);
        [self DrawBezierCurveInt:3 Curve:bezCurve];
		free((void *)bezCurve);
		return;
    }
    
    /*  Parameterize points, and attempt to fit curve */
    u = ChordLengthParameterize(d, first, last);
    bezCurve = [self GenerateBezierPoints:d First:first Last:last UPrime:u THat1:tHat1 THat2:tHat2];
    
    /*  Find max deviation of points to fitted curve */
    maxError = [self ComputeMaxErrorPoints:d First:first Last:last Curve:bezCurve Param:u maxError:&splitPoint];
    if (maxError < error) {
        //NSLog(@"Met error criteria --- 3 points");
		[self DrawBezierCurveInt:3 Curve:bezCurve];
		free((void *)u);
		free((void *)bezCurve);
		return;
    }
    
    
    /*  If error not too large, try some reparameterization  */
    /*  and iteration */
    if (maxError < iterationError) {
		for (i = 0; i < maxIterations; i++) {
            uPrime = [self ReparameterizePoints:d First:first Last:last Current:u TheCurve:bezCurve];
	    	free((void *)bezCurve);
            bezCurve = [self GenerateBezierPoints:d First:first Last:last UPrime:uPrime THat1:tHat1 THat2:tHat2];
            maxError = [self ComputeMaxErrorPoints:d First:first Last:last Curve:bezCurve Param:uPrime maxError:&splitPoint];
	    	if (maxError < error) {
                //NSLog(@"Met error criteria at segment %d --- 3 points", i);
                [self DrawBezierCurveInt:3 Curve:bezCurve];
                free((void *)u);
                free((void *)bezCurve);
                free((void *)uPrime);
                return;
            }
            free((void *)u);
            u = uPrime;
        }
    }
    
    /* Fitting failed -- split at max error point and fit recursively */
    //NSLog(@"Fitting failed --- splitting");
    
    free((void *)u);
    free((void *)bezCurve);
    tHatCenter = ComputeCenterTangent(d, splitPoint);
    [self FitCubicPoints:d First:first Last:splitPoint THat1:tHat1 THat2:tHatCenter TheError:error];
    V2Negate(&tHatCenter);
    [self FitCubicPoints:d First:splitPoint Last:last THat1:tHatCenter THat2:tHat2 TheError:error];
}

-(BezierCurve) GenerateBezierPoints:(Point2 *)d First:(int)first Last:(int)last UPrime:(double *)uPrime THat1:(Vector2)tHat1 THat2:(Vector2)tHat2 {
    int 	i;
    Vector2 	A[MAXPOINTS][2];	/* Precomputed rhs for eqn	*/
    int 	nPts;			/* Number of pts in sub-curve */
    double 	C[2][2];			/* Matrix C		*/
    double 	X[2];			/* Matrix X			*/
    double 	det_C0_C1,		/* Determinants of matrices	*/
    det_C0_X,
    det_X_C1;
    double 	alpha_l,		/* Alpha values, left and right	*/
    alpha_r;
    Vector2 	tmp;			/* Utility variable		*/
    BezierCurve	bezCurve;	/* RETURN bezier curve ctl pts	*/
    
    bezCurve = (Point2 *)malloc(4 * sizeof(Point2));
    nPts = last - first + 1;
    
    
    /* Compute the A's	*/
    for (i = 0; i < nPts; i++) {
		Vector2		v1, v2;
		v1 = tHat1;
		v2 = tHat2;
		V2Scale(&v1, B1(uPrime[i]));
		V2Scale(&v2, B2(uPrime[i]));
		A[i][0] = v1;
		A[i][1] = v2;
    }
    
    /* Create the C and X matrices	*/
    C[0][0] = 0.0;
    C[0][1] = 0.0;
    C[1][0] = 0.0;
    C[1][1] = 0.0;
    X[0]    = 0.0;
    X[1]    = 0.0;
    
    for (i = 0; i < nPts; i++) {
        C[0][0] += V2Dot(&A[i][0], &A[i][0]);
		C[0][1] += V2Dot(&A[i][0], &A[i][1]);
        /*					C[1][0] += V2Dot(&A[i][0], &A[i][1]);*/	
		C[1][0] = C[0][1];
		C[1][1] += V2Dot(&A[i][1], &A[i][1]);
        
		tmp = V2SubII(d[first + i],
                      V2AddII(
                              V2ScaleIII(d[first], B0(uPrime[i])),
                              V2AddII(
                                      V2ScaleIII(d[first], B1(uPrime[i])),
                                      V2AddII(
                                              V2ScaleIII(d[last], B2(uPrime[i])),
                                              V2ScaleIII(d[last], B3(uPrime[i]))))));
        
        
        X[0] += V2Dot(&A[i][0], &tmp);
        X[1] += V2Dot(&A[i][1], &tmp);
    }
    
    /* Compute the determinants of C and X	*/
    det_C0_C1 = C[0][0] * C[1][1] - C[1][0] * C[0][1];
    det_C0_X  = C[0][0] * X[1]    - C[1][0] * X[0];
    det_X_C1  = X[0]    * C[1][1] - X[1]    * C[0][1];
    
    /* Finally, derive alpha values	*/
    alpha_l = (det_C0_C1 == 0) ? 0.0 : det_X_C1 / det_C0_C1;
    alpha_r = (det_C0_C1 == 0) ? 0.0 : det_C0_X / det_C0_C1;
    
    /* If alpha negative, use the Wu/Barsky heuristic (see text) */
    /* (if alpha is 0, you get coincident control points that lead to
     * divide by zero in any subsequent NewtonRaphsonRootFind() call. */
    double segLength = V2DistanceBetween2Points(&d[last], &d[first]);
    double epsilon = 1.0e-6 * segLength;
    if (alpha_l < epsilon || alpha_r < epsilon)
    {
		/* fall back on standard (probably inaccurate) formula, and subdivide further if needed. */
		double dist = segLength / 3.0;
		bezCurve[0] = d[first];
		bezCurve[3] = d[last];
		V2Add(&bezCurve[0], V2Scale(&tHat1, dist), &bezCurve[1]);
		V2Add(&bezCurve[3], V2Scale(&tHat2, dist), &bezCurve[2]);
		return (bezCurve);
    }
    
    /*  First and last control points of the Bezier curve are */
    /*  positioned exactly at the first and last data points */
    /*  Control points 1 and 2 are positioned an alpha distance out */
    /*  on the tangent vectors, left and right, respectively */
    bezCurve[0] = d[first];
    bezCurve[3] = d[last];
    V2Add(&bezCurve[0], V2Scale(&tHat1, alpha_l), &bezCurve[1]);
    V2Add(&bezCurve[3], V2Scale(&tHat2, alpha_r), &bezCurve[2]);
    return (bezCurve);
}

-(double *) ReparameterizePoints:(Point2 *)d First:(int)first Last:(int)last Current:(double *)u TheCurve:(BezierCurve)bezCurve {
    int 	nPts = last-first+1;	
    int 	i;
    double	*uPrime;		/*  New parameter values	*/
    
    uPrime = (double *)malloc(nPts * sizeof(double));
    for (i = first; i <= last; i++) {
        uPrime[i-first] = [self NewtonRaphsonRootFindCurve:bezCurve Point:d[i] Param:u[i-first]];
    }
    return (uPrime);
}

-(double) NewtonRaphsonRootFindCurve:(BezierCurve)Q Point:(Point2)P Param:(double)u {
    double 		numerator, denominator;
    Point2 		Q1[3], Q2[2];	/*  Q' and Q''			*/
    Point2		Q_u, Q1_u, Q2_u; /*u evaluated at Q, Q', & Q''	*/
    double 		uPrime;		/*  Improved u			*/
    int 		i;
    
    /* Compute Q(u)	*/
    Q_u = [self BezierIIDegree:3 Point:Q Param:u];
    
    /* Generate control vertices for Q'	*/
    for (i = 0; i <= 2; i++) {
		Q1[i].x = (Q[i+1].x - Q[i].x) * 3.0;
		Q1[i].y = (Q[i+1].y - Q[i].y) * 3.0;
    }
    
    /* Generate control vertices for Q'' */
    for (i = 0; i <= 1; i++) {
		Q2[i].x = (Q1[i+1].x - Q1[i].x) * 2.0;
		Q2[i].y = (Q1[i+1].y - Q1[i].y) * 2.0;
    }
    
    /* Compute Q'(u) and Q''(u)	*/
    Q1_u = [self BezierIIDegree:2 Point:Q1 Param:u];
    Q2_u = [self BezierIIDegree:1 Point:Q2 Param:u];
    
    /* Compute f(u)/f'(u) */
    numerator = (Q_u.x - P.x) * (Q1_u.x) + (Q_u.y - P.y) * (Q1_u.y);
    denominator = (Q1_u.x) * (Q1_u.x) + (Q1_u.y) * (Q1_u.y) +
    (Q_u.x - P.x) * (Q2_u.x) + (Q_u.y - P.y) * (Q2_u.y);
    if (denominator == 0.0f) return u;
    
    /* u = u - f(u)/f'(u) */
    uPrime = u - (numerator/denominator);
    return (uPrime);
}

-(Point2) BezierIIDegree:(int)degree Point:(Point2 *)V Param:(double)t {
    int 	i, j;		
    Point2 	Q;	        /* Point on curve at parameter t	*/
    Point2 	*Vtemp;		/* Local copy of control points		*/
    
    /* Copy array	*/
    Vtemp = (Point2 *)malloc((unsigned)((degree+1) 
                                        * sizeof (Point2)));
    for (i = 0; i <= degree; i++) {
		Vtemp[i] = V[i];
    }
    
    /* Triangle computation	*/
    for (i = 1; i <= degree; i++) {	
		for (j = 0; j <= degree-i; j++) {
	    	Vtemp[j].x = (1.0 - t) * Vtemp[j].x + t * Vtemp[j+1].x;
	    	Vtemp[j].y = (1.0 - t) * Vtemp[j].y + t * Vtemp[j+1].y;
		}
    }
    
    Q = Vtemp[0];
    free((void *)Vtemp);
    return Q;
}
/*
 *  B0, B1, B2, B3 :
 *	Bezier multipliers
 */
static double B0(u)
double	u;
{
    double tmp = 1.0 - u;
    return (tmp * tmp * tmp);
}


static double B1(u)
double	u;
{
    double tmp = 1.0 - u;
    return (3 * u * (tmp * tmp));
}

static double B2(u)
double	u;
{
    double tmp = 1.0 - u;
    return (3 * u * u * tmp);
}

static double B3(u)
double	u;
{
    return (u * u * u);
}



/*
 * ComputeLeftTangent, ComputeRightTangent, ComputeCenterTangent :
 *Approximate unit tangents at endpoints and "center" of digitized curve
 */
static Vector2 ComputeLeftTangent(d, end)
Point2	*d;			/*  Digitized points*/
int		end;		/*  Index to "left" end of region */
{
    Vector2	tHat1;
    tHat1 = V2SubII(d[end+1], d[end]);
    tHat1 = *V2Normalize(&tHat1);
    return tHat1;
}

static Vector2 ComputeRightTangent(d, end)
Point2	*d;			/*  Digitized points		*/
int		end;		/*  Index to "right" end of region */
{
    Vector2	tHat2;
    tHat2 = V2SubII(d[end-1], d[end]);
    tHat2 = *V2Normalize(&tHat2);
    return tHat2;
}


static Vector2 ComputeCenterTangent(d, center)
Point2	*d;			/*  Digitized points			*/
int		center;		/*  Index to point inside region	*/
{
    Vector2	V1, V2, tHatCenter;
    
    V1 = V2SubII(d[center-1], d[center]);
    V2 = V2SubII(d[center], d[center+1]);
    tHatCenter.x = (V1.x + V2.x)/2.0;
    tHatCenter.y = (V1.y + V2.y)/2.0;
    tHatCenter = *V2Normalize(&tHatCenter);
    return tHatCenter;
}


/*
 *  ChordLengthParameterize :
 *	Assign parameter values to digitized points 
 *	using relative distances between points.
 */
static double *ChordLengthParameterize(d, first, last)
Point2	*d;			/* Array of digitized points */
int		first, last;		/*  Indices defining region	*/
{
    int		i;	
    double	*u;			/*  Parameterization		*/
    
    u = (double *)malloc((unsigned)(last-first+1) * sizeof(double));
    
    u[0] = 0.0;
    for (i = first+1; i <= last; i++) {
		u[i-first] = u[i-first-1] +
        V2DistanceBetween2Points(&d[i], &d[i-1]);
    }
    
    for (i = first + 1; i <= last; i++) {
		u[i-first] = u[i-first] / u[last-first];
    }
    
    return(u);
}




/*
 *  ComputeMaxError :
 *	Find the maximum squared distance of digitized points
 *	to fitted curve.
 */
-(double) ComputeMaxErrorPoints:(Point2 *)d First:(int)first Last:(int)last Curve:(BezierCurve)bezCurve Param:(double *)u maxError:(int *)splitPoint {
    int		i;
    double	maxDist;		/*  Maximum error		*/
    double	dist;		/*  Current error		*/
    Point2	P;			/*  Point on curve		*/
    Vector2	v;			/*  Vector from point to curve	*/
    
    *splitPoint = (last - first + 1)/2;
    maxDist = 0.0;
    for (i = first + 1; i < last; i++) {
        P = [self BezierIIDegree:3 Point:bezCurve Param:u[i-first]];
		v = V2SubII(P, d[i]);
		dist = V2SquaredLength(&v);
		if (dist >= maxDist) {
	    	maxDist = dist;
	    	*splitPoint = i;
		}
    }
    return (maxDist);
}
static Vector2 V2AddII(a, b)
Vector2 a, b;
{
    Vector2	c;
    c.x = a.x + b.x;  c.y = a.y + b.y;
    return (c);
}
static Vector2 V2ScaleIII(v, s)
Vector2	v;
double	s;
{
    Vector2 result;
    result.x = v.x * s; result.y = v.y * s;
    return (result);
}

static Vector2 V2SubII(a, b)
Vector2	a, b;
{
    Vector2	c;
    c.x = a.x - b.x; c.y = a.y - b.y;
    return (c);
}

- (void) dealloc
{
	[super dealloc];
}


@end