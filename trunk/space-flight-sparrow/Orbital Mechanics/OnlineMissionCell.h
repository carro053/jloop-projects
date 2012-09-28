//
//  OnlineMissionCell.h
//  Space Flight
//
//  Created by Michael Stratford on 7/3/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface OnlineMissionCell : UITableViewCell
@property (strong, nonatomic) IBOutlet UIImageView *imageView;
@property (strong, nonatomic) IBOutlet UILabel *title;
@property (strong, nonatomic) IBOutlet UILabel *rating;
@property (strong, nonatomic) IBOutlet UILabel *submitted;
@property (strong, nonatomic) IBOutlet UIButton *viewButton;

@end
