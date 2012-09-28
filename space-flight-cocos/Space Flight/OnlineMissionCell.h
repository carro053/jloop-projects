//
//  OnlineMissionCell.h
//  Space Flight
//
//  Created by Michael Stratford on 7/3/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface OnlineMissionCell : UITableViewCell
@property (retain, nonatomic) IBOutlet UIImageView *imageView;
@property (retain, nonatomic) IBOutlet UILabel *title;
@property (retain, nonatomic) IBOutlet UILabel *rating;
@property (retain, nonatomic) IBOutlet UILabel *submitted;
@property (retain, nonatomic) IBOutlet UIButton *viewButton;

@end
